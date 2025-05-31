<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\UserService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


final class UserController extends AbstractController
{
    private $httpClient;
    private $flouciApiKey;
    private $flouciApiSecret;
    private $flouciTrackingId;
    private $isFlouciTestMode;
    private $edinarApiKey;
    private $edinarApiSecret;
    private $edinarMerchantId;
    private $requestStack;
    private $userService;

    public function __construct(HttpClientInterface $httpClient,RequestStack $rs, UserService $userService)
    {
        $this->httpClient = $httpClient;
        $this->flouciApiKey = $_ENV['FLOUCI_API_KEY'];
        $this->flouciApiSecret = $_ENV['FLOUCI_API_SECRET'];
        $this->flouciTrackingId = $_ENV['FLOUCI_TRACKING_ID'];
        $this->isFlouciTestMode = filter_var($_ENV['FLOUCI_TEST_MODE'], FILTER_VALIDATE_BOOLEAN);
        $this->edinarApiKey = $_ENV['EDINAR_API_KEY'];
        $this->edinarApiSecret = $_ENV['EDINAR_API_SECRET'];
        $this->edinarMerchantId = $_ENV['EDINAR_MERCHANT_ID'];
        $this->requestStack= $rs;
        $this->userService = $userService;
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('user/account.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/account', name: 'app_user_account')]
    public function account(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_main');
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
        ]);
    
    }

    #[Route('/user/update-personal-info', name: 'app_user_update_personal_info', methods: ['POST'])]
    public function updatePersonalInfo(Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        // Verify CSRF token
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('update_personal_info', $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token');
            return $this->redirectToRoute('app_user_account');
        }

        $data = [
            'firstName' => $request->request->get('firstName'),
            'lastName'  => $request->request->get('lastName'),
            'email'     => $request->request->get('email'),
            'username'  => $request->request->get('username'),
        ];

        $result = $this->userService->updatePersonalInfo($user, $data);

        $this->addFlash($result['success'] ? 'success' : 'error', $result['message']);
        return $this->redirectToRoute('app_user_account');
    }

    #[Route('/user/checkout', name: 'app_user_checkout')]
    public function checkout(Request $request, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $cart = $this->getUser()->getCart();
        $shippingPrice = $request->request->get('shipping_price');
        if ($shippingPrice === null) {
            $shippingPrice = $session->get('shipping_price', 4.99);
        } else {
            $shippingPrice = (float) $shippingPrice;
            $session->set('shipping_price', $shippingPrice);
        }
        $session->set('cart', $cart);

        return $this->render('user/checkout.html.twig', [
            'controller_name' => 'UserController',
            'cart' => $cart,
            'shipping_price' => $shippingPrice,
        ]);
    }

    #[Route('/user/cart', name: 'app_user_cart')]
    public function cart(): Response
    {
        return $this->render('user/cart.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/cart/add/{pid}/{qte}', name: 'app_user_cart_add', requirements: ['pid' => '\d+', 'qte' => '-?\d+'], defaults: ['qte' => 1])]
    public function addToCart(ManagerRegistry $doctrine, int $pid, int $qte, SessionInterface $session) {
        $prod_rep = $doctrine->getRepository(Product::class);
        $product = $prod_rep->find($pid);

        if (!$product) {
            return $this->redirectToRoute('app_user_cart');
        }

        if (!$this->getUser()) {
            $cart = $session->get('cart', new Cart());
        } else {
            $user = $this->getUser();
            $cart = $user->getCart();
        }

        $orders = $cart->getOrders();
        foreach ($orders as $od) {
            $prod = $od->getProduct();
            if ($prod->getId() === $product->getId()) {
                $od->setQuantity($od->getQuantity() + $qte);
                $cart->updatePrixTotal();
                if ($this->getUser()) {
                    $doctrine->getManager()->persist($od);
                    $doctrine->getManager()->flush();
                } else {
                    $session->set('cart', $cart);
                }
                return $this->redirectToRoute('app_user_cart');
            }
        }

        $order = new Order();
        $order->setProduct($product)
            ->setDate(new DateTime())
            ->setStatus("Pending")
            ->setQuantity($qte);

        $cart->addOrder($order);

        if ($this->getUser()) {
            $cart->setUser($this->getUser());
            $doctrine->getManager()->persist($cart);
            $doctrine->getManager()->flush();
        } else {
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_user_cart');
    }

    #[Route('/user/cart/remove/{pid}', name: 'app_user_cart_remove', requirements: ['pid' => '\d+'])]
    public function removeFromCart(ManagerRegistry $doctrine, int $pid, SessionInterface $session): Response
    {
        $prod_rep = $doctrine->getRepository(Product::class);
        $product = $prod_rep->find($pid);

        if (!$product) {
            return $this->redirectToRoute('app_user_cart');
        }

        if (!$this->getUser()) {
            $cart = $session->get('cart', new Cart());
        } else {
            $user = $this->getUser();
            $cart = $user->getCart();
        }

        $orders = $cart->getOrders();
        foreach ($orders as $order) {
            if ($order->getProduct()->getId() == $product->getId()) {
                $cart->removeOrder($order);
                break;
            }
        }

        if ($this->getUser()) {
            $doctrine->getManager()->persist($cart);
            $doctrine->getManager()->flush();
        } else {
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_user_cart');
    }

    #[Route('/user/cart/clear', name: 'app_user_cart_clear')]
    public function clearCart(ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            $session->set('cart', new Cart());
        } else {
            $user = $this->getUser();
            $cart = $user->getCart();
            $cart->clearOrders();
            $doctrine->getManager()->persist($cart);
            $doctrine->getManager()->flush();
        }

        return $this->redirectToRoute('app_user_cart');
    }

    #[Route('/user/cart/update', name: 'app_user_cart_update', methods: ['POST'])]
    public function updateCart(Request $request, ManagerRegistry $doctrine): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quantities = $request->request->get('quantities', []);
        $orderIds = $request->request->get('order_ids', []);

        if (empty($quantities) || empty($orderIds)) {
            $this->addFlash('error', 'No quantities provided.');
            return $this->redirectToRoute('app_user_cart');
        }

        $orderRepository = $doctrine->getRepository(Order::class);
        $cart = $this->getUser()->getCart();
        $entityManager = $doctrine->getManager();

        $result = $this->userService->updateCart($cart, $quantities, $orderIds, $orderRepository, $entityManager);

        if (!$result['success']) {
            $this->addFlash('error', implode(' ', $result['errors']));
        } else {
            $this->addFlash('success', 'Cart updated successfully.');
        }

        return $this->redirectToRoute('app_user_cart');
    }

    #[Route('/user/wishlist/add/{pid}', name: 'app_user_wishlist_add', requirements: ['pid' => '\d+'])]
    public function addToWishlist(ManagerRegistry $doctrine, int $pid, SessionInterface $session) {
        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');

        $prod_rep = $doctrine->getRepository(Product::class);
        $product = $prod_rep->find($pid);

        if (!$product) {
            return $this->redirectToRoute('app_user_account');
        }

        if (!$this->getUser()) {
            $wishlist = $session->get('wishlist', new Wishlist());
        } else {
            $user = $this->getUser();
            $wishlist = $user->getWishlist();
        }

        foreach ($wishlist->getWishes() as $wish) {
            if ($wish->getProduct()->getId() === $pid) {
                if ($referer) {
                    return $this->redirect($referer);
                }
                return $this->redirectToRoute('app_user_account');
            }
        }

        $wish = new Wish();
        $wish->setProduct($product);

        $wishlist->addWish($wish);
        $wish->setWishlist($wishlist);

        if ($this->getUser()) {
            $doctrine->getManager()->persist($wish);
            $doctrine->getManager()->flush();
        } else {
            $session->set('wishlist', $wishlist);
        }

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_user_account');
    }

    #[Route('/user/wishlist/remove/{pid}', name: 'app_user_wishlist_remove', requirements: ['pid' => '\d+'])]
    public function removeFromWishlist(ManagerRegistry $doctrine, int $pid, SessionInterface $session) {
        $prod_rep = $doctrine->getRepository(Product::class);
        $product = $prod_rep->find($pid);

        if (!$this->getUser()) {
            $wishlist = $session->get('wishlist', []);
        } else {
            $user = $this->getUser();
            $wishlist = $user->getWishlist();
        }

        foreach ($wishlist->getWishes() as $wish) {
            if ($wish->getProduct()->getId() == $pid) {
                $wishlist->removeWish($wish);
                $doctrine->getManager()->remove($wish);
                break;
            }
        }

        if ($this->getUser()) {
            $doctrine->getManager()->persist($wishlist);
            $doctrine->getManager()->flush();
        } else {
            $session->set('wishlist', $wishlist);
        }

        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirect($this->generateUrl('app_user_account') . '#wishlist');
    }

    #[Route('/user/process-payment', name: 'app_user_process_payment')]
    public function processPayment(Request $request, SessionInterface $session): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart = $this->getUser()->getCart();
        $shippingPrice = (float) $session->get('shipping_price', 4.99);
        $paymentMethod = $request->request->get('payment_method');

        $result = $this->userService->processPayment(
            $cart,
            $shippingPrice,
            $paymentMethod,
            $this->httpClient,
            $this->flouciApiKey,
            $this->flouciTrackingId,
            $this->isFlouciTestMode,
            $this->edinarApiKey,
            $this->edinarMerchantId
        );

        if ($result['success']) {
            if ($result['method'] === 'flouci') {
                $session->set('flouci_payment_id', $result['payment_id']);
            } elseif ($result['method'] === 'edinar') {
                $session->set('edinar_payment_id', $result['payment_id']);
            }
            return $this->redirect($result['payment_url']);
        } else {
            $this->addFlash('error', $result['error']);
            return $this->redirectToRoute('app_user_checkout');
        }
    }

    #[Route('/user/payment/success', name: 'app_user_payment_success')]
    public function paymentSuccess(SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $flouciPaymentId = $session->get('flouci_payment_id');
        $edinarPaymentId = $session->get('edinar_payment_id');

        if ($flouciPaymentId) {
            // Verify Flouci payment status
            try {
                $apiUrl = $this->isFlouciTestMode
                    ? "https://sandbox.flouci.com/v1/payments/{$flouciPaymentId}"
                    : "https://api.flouci.com/v1/payments/{$flouciPaymentId}";

                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->flouciApiKey,
                    ],
                ]);

                $data = $response->toArray();
                if ($data['status'] === 'success') {
                    $cart = $this->getUser()->getCart();
                    foreach ($cart->getOrders() as $order) {
                        $order->setStatus('Paid');
                        $doctrine->getManager()->persist($order);
                    }
                    $cart->clearOrders();
                    $doctrine->getManager()->persist($cart);
                    $doctrine->getManager()->flush();

                    $session->remove('flouci_payment_id');
                    $this->addFlash('success', 'Flouci payment successful! Thank you for your order.');
                    return $this->redirectToRoute('app_user_account');
                } else {
                    $this->addFlash('error', 'Flouci payment not completed.');
                    return $this->redirectToRoute('app_user_checkout');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error verifying Flouci payment: ' . $e->getMessage());
                return $this->redirectToRoute('app_user_checkout');
            }
        } elseif ($edinarPaymentId) {
            // Verify e-Dinar payment status
            try {
                $apiUrl = "https://api.gpgcheckout.com/v1/payments/{$edinarPaymentId}"; // Adjust based on provider
                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->edinarApiKey,
                    ],
                ]);

                $data = $response->toArray();
                if ($data['status'] === 'success') {
                    $cart = $this->getUser()->getCart();
                    foreach ($cart->getOrders() as $order) {
                        $order->setStatus('Paid');
                        $doctrine->getManager()->persist($order);
                    }
                    $cart->clearOrders();
                    $doctrine->getManager()->persist($cart);
                    $doctrine->getManager()->flush();

                    $session->remove('edinar_payment_id');
                    $this->addFlash('success', 'e-Dinar payment successful! Thank you for your order.');
                    return $this->redirectToRoute('app_user_account');
                } else {
                    $this->addFlash('error', 'e-Dinar payment not completed.');
                    return $this->redirectToRoute('app_user_checkout');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error verifying e-Dinar payment: ' . $e->getMessage());
                return $this->redirectToRoute('app_user_checkout');
            }
        }

        $this->addFlash('error', 'No payment ID found.');
        return $this->redirectToRoute('app_user_checkout');
    }

    #[Route('/user/payment/cancel', name: 'app_user_payment_cancel')]
    public function paymentCancel(SessionInterface $session): Response
    {
        $session->remove('flouci_payment_id');
        $session->remove('edinar_payment_id');
        $this->addFlash('error', 'Payment was cancelled.');
        return $this->redirectToRoute('app_user_checkout');
    }

    #[Route('/user/payment/callback', name: 'app_user_payment_callback')]
    public function paymentCallback(Request $request, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $flouciPaymentId = $session->get('flouci_payment_id');
        $edinarPaymentId = $session->get('edinar_payment_id');

        if ($flouciPaymentId) {
            try {
                $apiUrl = $this->isFlouciTestMode
                    ? "https://sandbox.flouci.com/v1/payments/{$flouciPaymentId}"
                    : "https://api.flouci.com/v1/payments/{$flouciPaymentId}";

                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->flouciApiKey,
                    ],
                ]);

                $data = $response->toArray();
                if ($data['status'] === 'success') {
                    $cart = $this->getUser()->getCart();
                    foreach ($cart->getOrders() as $order) {
                        $order->setStatus('Paid');
                        $doctrine->getManager()->persist($order);
                    }
                    $cart->clearOrders();
                    $doctrine->getManager()->persist($cart);
                    $doctrine->getManager()->flush();

                    $session->remove('flouci_payment_id');
                    return new Response('Flouci payment verified', 200);
                }

                return new Response('Flouci payment not successful', 400);
            } catch (\Exception $e) {
                return new Response('Error verifying Flouci payment: ' . $e->getMessage(), 500);
            }
        } elseif ($edinarPaymentId) {
            try {
                $apiUrl = "https://api.gpgcheckout.com/v1/payments/{$edinarPaymentId}"; // Adjust based on provider
                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->edinarApiKey,
                    ],
                ]);

                $data = $response->toArray();
                if ($data['status'] === 'success') {
                    $cart = $this->getUser()->getCart();
                    foreach ($cart->getOrders() as $order) {
                        $order->setStatus('Paid');
                        $doctrine->getManager()->persist($order);
                    }
                    $cart->clearOrders();
                    $doctrine->getManager()->persist($cart);
                    $doctrine->getManager()->flush();

                    $session->remove('edinar_payment_id');
                    return new Response('e-Dinar payment verified', 200);
                }

                return new Response('e-Dinar payment not successful', 400);
            } catch (\Exception $e) {
                return new Response('Error verifying e-Dinar payment: ' . $e->getMessage(), 500);
            }
        }

        return new Response('Invalid payment ID', 400);
    }

}

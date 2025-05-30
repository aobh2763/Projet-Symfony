<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Wish;
use App\Entity\Wishlist;
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

    public function __construct(HttpClientInterface $httpClient,RequestStack $rs)
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
    public function account(): Response
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
public function updatePersonalInfo(Request $request, ManagerRegistry $doctrine): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');
    $user = $this->getUser();
        $firstName = $request->request->get('firstName');
    $lastName = $request->request->get('lastName');
    $email = $request->request->get('email');
    $username = $request->request->get('username');
    
    $user->setFirstName($firstName);
    $user->setLastName($lastName);
    $user->setEmail($email);
    $user->setUsername($username);
    $entityManager = $doctrine->getManager();
    $entityManager->persist($user);
    $entityManager->flush();
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

    #[Route('/user/cart/update', name: 'app_user_cart_update', methods: ['POST'])]
    public function updateCart(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quantities = $request->request->all('quantities', []);
        $orderIds = $request->request->all('order_ids', []);

        if (empty($quantities) || empty($orderIds)) {
            $this->addFlash('error', 'No quantities provided.');
            return $this->redirectToRoute('app_user_cart');
        }

        $orderRepository = $doctrine->getRepository(Order::class);
        $cart = $this->getUser()->getCart();
        $entityManager = $doctrine->getManager();
        $errors = [];

        foreach ($orderIds as $orderId) {
            $quantity = isset($quantities[$orderId]) ? (int) $quantities[$orderId] : null;

            // Validate quantity
            if (!is_numeric($quantity) || $quantity < 1 || $quantity > 100) {
                $errors[] = "Invalid quantity for order {$orderId}.";
                continue;
            }

            // Find order
            $order = $orderRepository->find($orderId);
            if (!$order) {
                $errors[] = "Order not found: {$orderId}.";
                continue;
            }

            // Verify order belongs to user's cart
            if (!$cart->getOrders()->contains($order)) {
                $errors[] = "Order does not belong to your cart: {$orderId}.";
                continue;
            }

            // Update quantity
            $order->setQuantity($quantity);
        }

        if (!empty($errors)) {
            $this->addFlash('error', implode(' ', $errors));
            return $this->redirectToRoute('app_user_cart');
        }

        // Update cart total
        $cart->updatePrixTotal();

        try {
            $entityManager->persist($cart);
            $entityManager->flush();
            $this->addFlash('success', 'Cart updated successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to update cart: ' . $e->getMessage());
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

        return $this->redirectToRoute('app_user_account');
    }

    #[Route('/user/process-payment', name: 'app_user_process_payment')]
    public function processPayment(Request $request, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart = $this->getUser()->getCart();
        $cart->updatePrixTotal();
        $doctrine->getManager()->persist($cart);
        $doctrine->getManager()->flush();

        $paymentMethod = $request->request->get('payment_method');
        $shippingPrice = (float) $session->get('shipping_price', 4.99);
        $totalAmount = $cart->getPrixTotal() + $shippingPrice + ($cart->getPrixTotal() * 0.01);

        if ($paymentMethod === 'flouci') {
            try {
                $apiUrl = $this->isFlouciTestMode
                    ? 'https://sandbox.flouci.com/v1/payments'
                    : 'https://api.flouci.com/v1/payments';

                $response = $this->httpClient->request('POST', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->flouciApiKey,
                        'Content-Type' => 'application/json',
                        'X-Developer-Tracking-Id' => $this->flouciTrackingId,
                    ],
                    'json' => [
                        'amount' => $totalAmount * 1000, // Millimes (TND * 1000)
                        'currency' => 'TND',
                        'order_id' => 'FLOUCI_' . uniqid(),
                        'success_url' => 'http://localhost:8000/user/payment/success',
                        'cancel_url' => 'http://localhost:8000/user/payment/cancel',
                        'callback_url' => 'http://localhost:8000/user/payment/callback',
                    ],
                ]);

                $data = $response->toArray();
                if (isset($data['payment_url'])) {
                    $session->set('flouci_payment_id', $data['payment_id']);
                    return $this->redirect($data['payment_url']);
                }

                throw new \Exception('Failed to create Flouci payment');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Flouci payment initiation failed: ' . $e->getMessage());
                return $this->redirectToRoute('app_user_checkout');
            }
        } elseif ($paymentMethod === 'edinar') {
            try {
                // GPGCheckout API for e-Dinar (hypothetical, replace with actual endpoint)
                $apiUrl = 'https://api.gpgcheckout.com/v1/payments'; // Adjust based on provider
                $response = $this->httpClient->request('POST', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->edinarApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'merchant_id' => $this->edinarMerchantId,
                        'amount' => $totalAmount * 1000, // Millimes (TND * 1000)
                        'currency' => 'TND',
                        'order_id' => 'EDINAR_' . uniqid(),
                        'success_url' => 'http://localhost:8000/user/payment/success',
                        'cancel_url' => 'http://localhost:8000/user/payment/cancel',
                        'callback_url' => 'http://localhost:8000/user/payment/callback',
                        'payment_method' => 'edinar', // Specify e-Dinar wallet/card
                    ],
                ]);

                $data = $response->toArray();
                if (isset($data['payment_url'])) {
                    $session->set('edinar_payment_id', $data['payment_id']);
                    return $this->redirect($data['payment_url']);
                }

                throw new \Exception('Failed to create e-Dinar payment');
            } catch (\Exception $e) {
                $this->addFlash('error', 'e-Dinar payment initiation failed: ' . $e->getMessage());
                return $this->redirectToRoute('app_user_checkout');
            }
        }

        $this->addFlash('error', 'Invalid payment method selected.');
        return $this->redirectToRoute('app_user_checkout');
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
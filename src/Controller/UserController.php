<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UserController extends AbstractController
{

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->flouciApiKey = $_ENV['FLOUCI_API_KEY']; // Set in .env
        $this->flouciApiSecret = $_ENV['FLOUCI_API_SECRET']; // Set in .env
    }
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/account', name: 'app_user_account')]
    public function account(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('user/account.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/checkout', name: 'app_user_checkout')]
    public function checkout(Request $request,SessionInterface $session): Response
    {   
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart = $this->getUser()->getCart();
        $shippingPrice = $request->request->get('shipping_price');
        if ($shippingPrice === null) {
            $shippingPrice = $session->get('shipping_price', 4.99); // Valeur par défaut
        } else {
            $shippingPrice = (float) $shippingPrice;
            $session->set('shipping_price', $shippingPrice);
        }
        $session->set('cart', $cart);

        return $this->render('user/checkout.html.twig', [
            'controller_name' => 'UserController',
            'cart' => $cart,
            'shipping_price' => $shippingPrice
        ]);
    }

    #[Route('/user/cart', name: 'app_user_cart')]
    public function cart(): Response
    {
        return $this->render('user/cart.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/cart/add/{pid}/{qte}', name: 'app_user_cart_add', requirements: ['pid' => '\d+', 'qte' => '\d+'], defaults: ['qte' => 1])]
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
    public function removeFromCart(ManagerRegistry $doctrine, int $pid, SessionInterface $session) {
        $prod_rep = $doctrine->getRepository(Product::class);
        $product = $prod_rep->find($pid);

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
    public function clearCart(ManagerRegistry $doctrine, SessionInterface $session) {
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
        $totalAmount = $cart->getPrixtotal() + $shippingPrice + ($cart->getPrixtotal() * 0.01);

        if ($paymentMethod === 'flouci') {
            try {
                // Create Flouci payment
                $response = $this->httpClient->request('POST', 'https://api.flouci.com/v1/payments', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->flouciApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'amount' => $totalAmount * 1000,
                        'currency' => 'TND',
                        'order_id' => 'ORDER_' . uniqid(),
                        'success_url' => $this->generateUrl('app_user_payment_success', [], true),
                        'cancel_url' => $this->generateUrl('app_user_payment_cancel', [], true),
                        'callback_url' => $this->generateUrl('app_user_payment_callback', [], true),
                    ],
                ]);

                $data = $response->toArray();
                if (isset($data['payment_url'])) {
                    // Store payment ID in session for verification
                    $session->set('flouci_payment_id', $data['payment_id']);
                    return $this->redirect($data['payment_url']);
                }

                throw new \Exception('Failed to create Flouci payment');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Payment initiation failed: ' . $e->getMessage());
                return $this->redirectToRoute('app_user_checkout');
            }
        } elseif ($paymentMethod === 'stripe') {
            // Handle Stripe payment (implement if needed)
            $this->addFlash('error', 'Stripe payment not implemented yet.');
            return $this->redirectToRoute('app_user_checkout');
        }

        $this->addFlash('error', 'Invalid payment method selected.');
        return $this->redirectToRoute('app_user_checkout');
    }

    #[Route('/user/payment/success', name: 'app_user_payment_success')]
    public function paymentSuccess(SessionInterface $session): Response
    {
        $this->addFlash('success', 'Payment successful! Thank you for your order.');
        $session->remove('flouci_payment_id');
        return $this->redirectToRoute('app_user_account');
    }

    #[Route('/user/payment/cancel', name: 'app_user_payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Payment was cancelled.');
        return $this->redirectToRoute('app_user_checkout');
    }

    #[Route('/user/payment/callback', name: 'app_user_payment_callback')]
    public function paymentCallback(Request $request, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $paymentId = $session->get('flouci_payment_id');
        if (!$paymentId) {
            return new Response('Invalid payment ID', 400);
        }

        try {
            $response = $this->httpClient->request('GET', "https://api.flouci.com/v1/payments/{$paymentId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->flouciApiKey,
                ],
            ]);

            $data = $response->toArray();
            if ($data['status'] === 'success') {
                // Update order status
                $cart = $this->getUser()->getCart();
                foreach ($cart->getOrders() as $order) {
                    $order->setStatus('Paid');
                    $doctrine->getManager()->persist($order);
                }
                $cart->clearOrders();
                $doctrine->getManager()->persist($cart);
                $doctrine->getManager()->flush();

                $session->remove('flouci_payment_id');
                return new Response('Payment verified', 200);
            }

            return new Response('Payment not successful', 400);
        } catch (\Exception $e) {
            return new Response('Error verifying payment: ' . $e->getMessage(), 500);
        }
    }
}
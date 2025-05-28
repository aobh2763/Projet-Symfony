<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Service\AdminService;
use App\Service\UserService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, RequestStack};
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{

    public function __construct(
        private RequestStack $requestStack,
        private UserService $userService
    ){}

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
    public function checkout(): Response
    {   
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('user/checkout.html.twig', [
            'controller_name' => 'UserController',
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

    //TODO: change this to a form submit (action)
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
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
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
}

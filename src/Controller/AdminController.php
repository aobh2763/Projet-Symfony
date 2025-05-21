<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/order-confirmation', name: 'app_admin_order_confirmation')]
    public function orderConfirmation(): Response
    {
        return $this->render('admin/order-confirmation.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/acount', name: 'app_admin_account')]
    public function account(): Response
    {
        return $this->render('admin/account.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\AdminService;

final class AdminController extends AbstractController
{
    public function __construct(
                private AdminService $adminService
        ){
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // visualise stats and give orders preview

        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function orders(): Response
    {
        // visualize list of orders

        return $this->render('admin/orders.html.twig', [
            'controller_name' => 'AdminController',
            'orders' => $this->adminService->getOrders()
        ]);
    }
}

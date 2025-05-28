<?php

namespace App\Controller;

use App\Form\CreateProductTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{Response, RequestStack};

use App\Service\AdminService;
use App\Entity\Product;
use App\Form\OrdersFilterTypeForm;

final class AdminController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private AdminService $adminService
    ){}

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_admin_orders');
    }

    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function getOrders(): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm(OrdersFilterTypeForm::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $filters = $form->isSubmitted() && $form->isValid()
            ? $form->getData()
            : [];

        $orders = $this->adminService->getOrders(null);

        return $this->render('admin/orders.html.twig', [
            'orders' => $orders,
            'form' => $form
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function getUsers(): Response
    {
        $users = $this->adminService->getUsers(null);

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }   
    
    #[Route('/admin/create-product', name: 'app_admin_create_product')]
    public function createProduct(): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $product = new Product();

        $form = $this->createForm(CreateProductTypeForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adminService->createProduct($product);
        }

        return $this->render('admin/create-product.html.twig', [
            'form' => $form
        ]);
    }   
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{Response, RequestStack};

use App\Service\AdminService;
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
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm(OrdersFilterTypeForm::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $filters = $form->isSubmitted() && $form->isValid()
            ? $form->getData()
            : [];

        $orders = $this->adminService->getOrders($filters);

        return $this->render('admin/dashboard.html.twig', [
            'orders' => $orders,
            'form' => $form
        ]);
    }
}

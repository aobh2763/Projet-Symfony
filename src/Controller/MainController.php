<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/products', name: 'app_products')]
    public function products(): Response
    {
        return $this->render('main/products.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    // TODO: root needs a product id
    #[Route('/main/product-details', name: 'app_product_details')]
    public function productDetails(): Response
    {
        return $this->render('main/product-details.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('main/login.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('main/register.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}

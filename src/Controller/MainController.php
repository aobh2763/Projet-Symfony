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

    #[Route('/main/category', name: 'app_category')]
    public function category(): Response
    {
        return $this->render('main/category.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('main/contact.html.twig', [
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

    #[Route('/main/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/blog-details', name: 'app_blog_details')]
    public function blogDetails(): Response
    {
        return $this->render('main/blog-details.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/blog', name: 'app_blog')]
    public function blog(): Response
    {
        return $this->render('main/blog.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/faq', name: 'app_faq')]
    public function faq(): Response
    {
        return $this->render('main/faq.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/product-details', name: 'app_product_details')]
    public function productDetails(): Response
    {
        return $this->render('main/product-details.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}

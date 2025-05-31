<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\{RequestStack, Response, RedirectResponse};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\{User, Cart, Wishlist};
use App\Form\{FilterTypeForm, RegistrationForm};
use App\Service\MainService;

final class MainController extends AbstractController
{

    public function __construct(
        private RequestStack $requestStack,
        private MainService $mainService
    ){}

    #[Route('/', name: 'app_main')]
    public function index(): Response {
        $this->onFirstVisit();

        $newcollection = $this->mainService->getRandomProducts(4);
        $showcase = $this->mainService->getHighestRated(16);
        
        return $this->render('main/index.html.twig', [
            'newcollection' => $newcollection,
            'showcase' => $showcase
        ]);
        
    }

    #[Route('/products', name: 'app_products')]
    public function products(): Response
    {
        $this->onFirstVisit();

        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm(FilterTypeForm::class, null, [
            'method' => 'GET',
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);

        $form->submit($request->query->all());


        $filters = $form->isSubmitted() && $form->isValid()
        ? [
            'name' => $form->get('name')->getData(),
            'type' => $form->get('type')->getData(),
            'range' => $form->get('range')->getData(),
            'sort' => $form->get('sort')->getData(),
        ]
        : [];

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 9);

        $products = $this->mainService->getProducts(filters: $filters, page: $page, limit: $limit);

        return $this->render('main/products.html.twig', [
            'products' => $products,
            'filterform' => $form,
            'filters' => $filters,
        ]);
    }

    #[Route('/products/{pid}', name: 'app_product_details', requirements: ['pid' => '\d+'])]
    public function productDetails(int $pid): Response
    {
        $this->onFirstVisit();
        
        $product = $this->mainService->getProduct($pid);

        return $this->render('main/product-details.html.twig', [
            'product' => $product
        ]);
    }

    public function onFirstVisit() {
        $session = $this->requestStack->getSession();

        if (!$session->has('firstVisit')) {
            $session->set('firstVisit', true);

            $cart = new Cart();
            $cart->setPrixtotal(0.0);

            $wishlist = new Wishlist();

            $session->set('cart', $cart);
            $session->set('wishlist', $wishlist);
        }
    }
    
    #[Route('/terms', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('main/termsandconditions.html.twig');
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('main/about.html.twig');
    }
}

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
    public function index(ProductRepository $productRepository): Response
    {
        $this->onFirstVisit();
        $products = $productRepository->findBy([], ['id' => 'DESC'], 6);
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'products' => $products,
        ]);
        
    }

    //TODO: paging through request params
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

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    //TODO: logout
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout() {
        return new RedirectResponse($this->generateUrl('app_main'));
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response {
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(): Response {
        $request = $this->requestStack->getCurrentRequest();
        $session = $this->requestStack->getSession();

        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->mainService->registerUser(
                $user, 
                $session->get('cart'), 
                $session->get('wishlist')
            );
        }

        return $this->render('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    // #[Route('/verify/email', name: 'app_verify_email')]
    // public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response {
    //     $this->onFirstVisit();
        
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     // validate email confirmation link, sets User::isVerified=true and persists
    //     try {
    //         /** @var User $user */
    //         $user = $this->getUser();
    //         $this->emailVerifier->handleEmailConfirmation($request, $user);
    //     } catch (VerifyEmailExceptionInterface $exception) {
    //         $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

    //         return $this->redirectToRoute('app_register');
    //     }

    //     $this->addFlash('success', 'Your email address has been verified.');

    //     return $this->redirectToRoute('app_main');
    // }

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
}

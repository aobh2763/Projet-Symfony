<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, RedirectResponse};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationForm;
use App\Service\MainService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

final class MainController extends AbstractController
{

    public function __construct(
                private MainService $mainService
    ){

    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $this->mainService->onFirstVisit();
        
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    //TODO: paging through request params
    #[Route('/products', name: 'app_products')]
    public function products(): Response
    {
        $this->mainService->onFirstVisit();

        $products = $this->mainService->getProducts();

        return $this->render('main/products.html.twig', [
            'products' => $products
            // 'filterform' => $view,
            // 'filters' => $request->query->all()
        ]);
    }

    #[Route('/products/{pid}', name: 'app_product_details', requirements: ['pid' => '\d+'])]
    public function productDetails(ManagerRegistry $doctrine, int $pid): Response
    {
        $this->mainService->onFirstVisit();
        
        $product = $this->mainService->getProduct($pid);

        return $this->render('main/product-details.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        $this->mainService->onFirstVisit();
        
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
        $this->mainService->onFirstVisit();
        
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response {
        $this->mainService->onFirstVisit();
        
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->mainService->registerUser($user);
        }

        return $this->render('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    // #[Route('/verify/email', name: 'app_verify_email')]
    // public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response {
    //     $this->mainService->onFirstVisit();
        
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
}

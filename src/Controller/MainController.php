<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\Wishlist;
use App\Form\FilterTypeForm;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class MainController extends AbstractController
{
    public EmailVerifier $emailVerifier;
    public SessionInterface $session;

    public function __construct(EmailVerifier $emailVerifier, RequestStack $requestStack) {
        $this->emailVerifier = $emailVerifier;
        $this->session = $requestStack->getSession();
    }

    public function onFirstVisit() {
        if (!$this->session->has('firstVisit')) {
            $this->session->set('firstVisit', true);

            $cart = new Cart();
            $cart->setPrixtotal(0.0);

            $wishlist = new Wishlist();

            $this->session->set('cart', $cart);
            $this->session->set('wishlist', $wishlist);
        }
    }

    #[Route('/', name: 'app_home')]
    public function home() {
        $this->onFirstVisit();

        return new RedirectResponse($this->generateUrl('app_main'));
    }

    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        $this->onFirstVisit();
        
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/products/{page}', name: 'app_products', defaults: ['page' => 1])]
    public function products(ManagerRegistry $doctrine, int $page, Request $request): Response
    {
        $this->onFirstVisit();
        
        $form = $this->createForm(FilterTypeForm::class, null, [
            'action' => $this->generateUrl('app_products', ['page' => $page]),
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $data = $form->getData();
        
        /** @var ProductRepository $prod_rep */
        $prod_rep = $doctrine->getRepository(Product::class);

        $products = $prod_rep->getFilteredProducts(
            $data['name'] ?? '',
            $data['type'] ?? 'any',
            $data['range'] ?? 'all',
            $data['sort'] ?? 'featured'
        );

        $view = $form->createView();

        return $this->render('main/products.html.twig', [
            'products' => $products,
            'page' => $page,
            'filterform' => $view,
            'filters' => $request->query->all()
        ]);
    }

    #[Route('/main/product-details/{pid}', name: 'app_product_details', requirements: ['pid' => '\d+'])]
    public function productDetails(ManagerRegistry $doctrine, int $pid): Response
    {
        $this->onFirstVisit();
        
        $prod_rep = $doctrine->getRepository(Product::class);

        $product = $prod_rep->find($pid);
        if (!$product) {
            return $this->render('main/404.html.twig', [
                'message' => 'Product not found.'
            ]);
        }

        return $this->render('main/product-details.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/main/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        $this->onFirstVisit();
        
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/main/logout', name: 'app_logout')]
    public function logout() {
        return new RedirectResponse($this->generateUrl('app_main'));
    }

    #[Route('/main/about', name: 'app_about')]
    public function about(): Response {
        $this->onFirstVisit();
        
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response {
        $this->onFirstVisit();
        
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);

            $sessionCart = $this->session->get('cart');
            if ($sessionCart) {
                foreach ($sessionCart->getOrders() as $order) {
                    $product = $order->getProduct();
                    if ($product && $product->getId()) {
                        $managedProduct = $entityManager->getRepository(Product::class)->find($product->getId());
                        $order->setProduct($managedProduct);
                    }
                    if (!$order->getId()) {
                        $entityManager->persist($order);
                    }
                }

                if (!$sessionCart->getId()) {
                    $entityManager->persist($sessionCart);
                }
                $user->setCart($sessionCart);
            } else {
                $user->setCart(new Cart());
            }

            $sessionWishlist = $this->session->get('wishlist');
            if ($sessionWishlist) {
                foreach ($sessionWishlist->getWishes() as $wishlistItem) {
                    $product = $wishlistItem->getProduct();
                    if ($product && $product->getId()) {
                        $managedProduct = $entityManager->getRepository(Product::class)->find($product->getId());
                        $wishlistItem->setProduct($managedProduct);
                    }
                    if (!$wishlistItem->getId()) {
                        $entityManager->persist($wishlistItem);
                    }
                }
                if (!$sessionWishlist->getId()) {
                    $entityManager->persist($sessionWishlist);
                }
                $user->setWishlist($sessionWishlist);
            } else {
                $user->setWishlist(new Wishlist());
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('gunstore@gmail.com', 'GunStore'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('main/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $security->login($user, AppCustomAuthenticator::class, 'main');
        }

        return $this->render('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response {
        $this->onFirstVisit();
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_main');
    }
}

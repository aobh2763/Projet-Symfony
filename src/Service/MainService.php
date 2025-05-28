<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

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

class MainService {

    private ?Request $request;
    private ?SessionInterface $session;

    public function __construct(
                private EntityManagerInterface $entityManager, 
                private EmailVerifier $emailVerifier, 
                private UserPasswordHasherInterface $userPasswordHasher, 
                private Security $security,
                RequestStack $requestStack, 
    ) {
        $this->session = $requestStack->getSession();
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getProducts(){
        $productsRepository = $this->entityManager->getRepository(Product::class);

        $page = max(1, (int) $this->request->query->get('page', 1));
        $limit = min(100, (int) $this->request->query->get('limit', 10));
        $offset = ($page - 1) * $limit;

        $products = $productsRepository->findBy([], null, $limit, $offset);

        return $products;
    }

    public function getProduct(int $id){
        $productsRepository = $this->entityManager->getRepository(Product::class);
        $product = $productsRepository->find($id);
        return $product;
    }

    public function registerUser(User $user): Response {
        $user->setCart($this->getSessionCart());
        $user->setWishlist($this->getSessionWishlist());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // generate a signed url and email it to the user
        // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
        //     (new TemplatedEmail())
        //         ->from(new Address('gunstore@gmail.com', 'GunStore'))
        //         ->to((string) $user->getEmail())
        //         ->subject('Please Confirm your Email')
        //         ->htmlTemplate('main/confirmation_email.html.twig')
        // );

        // do anything else you need here, like send an email

        return $this->security->login($user, AppCustomAuthenticator::class, 'main');
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

    public function getSessionCart() {
        $cart = new Cart();
        $sessionCart = $this->session->get('cart') ?? new Cart();

        foreach ($sessionCart->getOrders() as $order) {
            $product = $order->getProduct();

            if ($this->productExists($product->getId())) {
                $cart->addOrder($order);
            }
        }

        return $cart;
    }

    public function getSessionWishlist(){
        $wishlist = new Wishlist();
        $sessionWishlist = $this->session->get('wishlist') ?? new Wishlist();

        foreach ($sessionWishlist->getWishes() as $wish) {
            $product = $wish->getProduct();

            if ($this->productExists($product->getId())) {
                $wishlist->addWish($wish);
            }
        }
        
        return $wishlist;
    }

    public function productExists(int $id) {
        return $this->getProduct($id) !== null;
    }
}
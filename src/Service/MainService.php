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

    private SessionInterface $session;

    public function __construct(
                private EntityManagerInterface $entityManager, 
                private EmailVerifier $emailVerifier, 
                private UserPasswordHasherInterface $userPasswordHasher, 
                private Security $security,
                RequestStack $requestStack, 
    ) {
        $this->session = $requestStack->getSession();
    }

    public function getProducts(){
        $productsRepository = $this->entityManager->getRepository(Product::class);
        $products = $productsRepository->findAll();
        return $products;
    }

    public function getProduct(int $id){
        $productsRepository = $this->entityManager->getRepository(Product::class);
        $product = $productsRepository->find($id);
        return $product;
    }

    public function registerUser(User $user): Response {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));

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

    public function getSessionCart(){
        $sessionCart = $this->session->get('cart') ?? new Cart();

        foreach ($sessionCart->getOrders() as $order) {
            $product = $order->getProduct();

            if ($product && $product->getId()) {
                $managedProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
                $order->setProduct($managedProduct);
            }

            if (!$order->getId()) {
                $this->entityManager->persist($order);
            }
        }
        
        return $sessionCart;
    }

    public function getSessionWishlist(){
        $sessionWishlist = $this->session->get('wishlist') ?? new Wishlist();

        foreach ($sessionWishlist->getWishes() as $wishlistItem) {
            $product = $wishlistItem->getProduct();

            if ($product && $product->getId()) {
                $managedProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
                $wishlistItem->setProduct($managedProduct);
            }
            
            if (!$wishlistItem->getId()) {
                $this->entityManager->persist($wishlistItem);
            }
        }
        
        return $sessionWishlist;
    }
}
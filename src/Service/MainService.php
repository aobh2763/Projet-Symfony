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

use Knp\Component\Pager\PaginatorInterface;

class MainService {

    public function __construct(
        private PaginatorInterface $paginator,
        private EntityManagerInterface $entityManager, 
        private EmailVerifier $emailVerifier, 
        private Security $security,
    ) {}

    public function getProducts($filters, $page, $limit){
        $query = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->getQuery();

        $products = $this->paginator->paginate(
            $query,
            $page,
            $limit
        );

        return $products;
    }

    public function getProduct(int $id){
        $productsRepository = $this->entityManager->getRepository(Product::class);
        $product = $productsRepository->find($id);
        return $product;
    }

    public function registerUser(User $user, Cart $sessionCart, Wishlist $sessionWishlist): Response {
        $user->setCart($this->getCleanCart($sessionCart));
        $user->setWishlist($this->getCleanWishlist($sessionWishlist));

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

    public function getCleanCart(Cart $sessionCart) {
        $cart = new Cart();

        foreach ($sessionCart->getOrders() as $order) {
            $product = $order->getProduct();

            if ($this->productExists($product->getId())) {
                $cart->addOrder($order);
            }
        }

        return $cart;
    }

    public function getCleanWishlist(Wishlist $sessionWishlist){
        $wishlist = new Wishlist();

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
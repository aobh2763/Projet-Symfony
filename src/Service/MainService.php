<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

use App\Entity\Cart;
use App\Entity\Order;
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
use App\Entity\Wish;
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
    ) {}

    public function getProducts($filters, $page, $limit){
        $productsRepository = $this->entityManager->getRepository(Product::class);

        $query = $productsRepository->getFilteredProducts(
            $filters['name'] ?? '',
            $filters['type'] ?? 'any',
            $filters['range'] ?? 'all',
            $filters['sort'] ?? 'featured'
        );

        $products = $this->paginator->paginate(
            $query,
            $page,
            $limit,
            ['wrap-queries' => true, 'sortFieldParameterName' => null]
        );

        return $products;
    }

    public function getProduct(int $id){
        $productsRepository = $this->entityManager->getRepository(Product::class);
        $product = $productsRepository->find($id);
        return $product;
    }

    public function getRandomProducts(int $count = 4) {
        $productsRepository = $this->entityManager->getRepository(Product::class);

        return $productsRepository->findRandomProducts($count);
    }

    public function getHighestRated(int $limit = 16) {
        $productsRepository = $this->entityManager->getRepository(Product::class);

        return $productsRepository->findHighestRated($limit);
    }

    public function getCleanCart($sessionCart) {
        $cart = new Cart();

        foreach ($sessionCart->getOrders() as $order) {
            $productId = $order->getProduct()->getId();

            if ($this->productExists($productId)) {
                $product = $this->entityManager->getRepository(Product::class)->find($productId);

                $newOrder = new Order();
                $newOrder->setDate(new \DateTime());
                $newOrder->setStatus("Pending");
                $newOrder->setProduct($product);
                $newOrder->setQuantity($order->getQuantity());
                $newOrder->setCart($cart);

                $cart->addOrder($newOrder);
            }
        }

        return $cart;
    }

    public function getCleanWishlist(Wishlist $sessionWishlist){
        $wishlist = new Wishlist();

        foreach ($sessionWishlist->getWishes() as $wish) {
            $curProduct = $wish->getProduct();
            $productId = $wish->getProduct()->getId();

            if ($this->productExists($curProduct->getId())) {
                $product = $this->entityManager->getRepository(Product::class)->find($productId);

                $newWish = new Wish();
                $newWish->setProduct($product);
                $newWish->setWishlist($wishlist);

                $wishlist->addWish($newWish);
            }
        }
        
        return $wishlist;
    }

    public function productExists(int $id) {
        return $this->getProduct($id) !== null;
    }
}
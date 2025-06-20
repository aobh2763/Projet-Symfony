<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\{User, Order, Product};
use Knp\Component\Pager\PaginatorInterface;

class AdminService {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ){
    }

    public function getOrders($filters){
        $qb = $this->entityManager->getRepository(Order::class)
            ->createQueryBuilder('o');

        // Filter by name
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $filters['status'] = match($filters['status']) {
                'in-progress' => 'Pending',
                'delivered' => 'Delivered',
                'canceled' => 'Canceled',
            }; 

            $qb->andWhere('o.status LIKE :status')
               ->setParameter('status', '%' . $filters['status'] . '%');
        }

        // Filter by order date after a certain time
        if (!empty($filters['time']) && $filters['time'] !== 'all-time') {
            $qb->andWhere('o.date > :time')
               ->setParameter('time', $filters['time']);
        }

        $query = $qb->getQuery();

        $orders = $this->paginator->paginate(
            $query
        );

        return $orders;
    }

    public function getUsers(){
        $qb = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u');

        $query = $qb->getQuery();

        $users = $this->paginator->paginate(
            $query
        );

        return $users;
    }

    public function createProduct($product){
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function updateProduct($updatedProduct){
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->find($updatedProduct->getId());

        if ($product) {
            $product->setName($updatedProduct->getName());
            $product->setDescription($updatedProduct->getDescription());
            $product->setImage($updatedProduct->getImage());
            $product->setPrice($updatedProduct->getPrice());
            $product->setRating($updatedProduct->getRating());
            $product->setSale($updatedProduct->getSale());
            $product->setStock($updatedProduct->getStock());
            $product->setWeight($updatedProduct->getWeight());

            $this->entityManager->flush();
        }
    }

    public function deleteProduct($id){
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->find($id);

        if ($product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();        
        }
    }

    public function deleteUser($id){
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();        
        }
    }
}
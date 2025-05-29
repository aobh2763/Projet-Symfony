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
        if (!empty($filters['status'])) {
            $qb->andWhere('p.status LIKE :status')
               ->setParameter('status', '%' . $filters['status'] . '%');
        }

        // Filter by type
        if (!empty($filters['time'])) {
            $qb->andWhere('p.type = :type')
               ->setParameter('type', $filters['type']);
        }

        $query = $qb->getQuery();

        $orders = $this->paginator->paginate(
            $query
        );

        return $orders;
    }

    public function getUsers($filters){
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
        $product = $productRepository->find($updatedProduct->getProduct());

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

    //TODO: doesnt work if the product has orders on it
    public function deleteProduct($id){
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->find($id);

        if ($product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();        
        }
    }

}
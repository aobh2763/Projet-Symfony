<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Order;
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

        $products = $this->paginator->paginate(
            $query
        );

        return $products;
    }

}
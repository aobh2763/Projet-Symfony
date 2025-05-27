<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

use App\Entity\Order;

class AdminService {

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    public function getOrders(){
        $ordersRepository = $this->entityManager->getRepository(Order::class);
        return $ordersRepository->findAll();
    }

}
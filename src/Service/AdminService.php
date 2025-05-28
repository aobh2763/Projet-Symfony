<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Order;

class AdminService {

    
    public function __construct(
                private EntityManagerInterface $entityManager
    ){
    }

    public function getOrders(){
        $ordersRepository = $this->entityManager->getRepository(Order::class);
        return $ordersRepository->findAll();
    }

}
<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

class UserService {

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }
}
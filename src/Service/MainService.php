<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

class MainService {

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }
}
<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class UserService {

    public function __construct(
                private EntityManagerInterface $entityManager
    ){
    }
}
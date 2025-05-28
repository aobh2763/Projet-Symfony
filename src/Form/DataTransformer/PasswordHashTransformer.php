<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;

class PasswordHashTransformer implements DataTransformerInterface {
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    // From model to form
    public function transform($value) {
        return '';
    }

    // From form to model
    public function reverseTransform($value) {
        return $this->passwordHasher->hashPassword(new User(), $value);
    }
}
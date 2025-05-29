<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use App\Entity\{User, Cart, Wishlist};

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();

            $user->setUsername("user$i")
                ->setEmail("user$i@example.com")
                ->setFirstName("FirstName$i")
                ->setLastName("LastName$i")
                ->setIsVerified(true)
                ->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

            $cart = new Cart();
            $user->setCart($cart);

            $wishlist = new Wishlist();
            $user->setWishlist($wishlist);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['users'];
    }
}

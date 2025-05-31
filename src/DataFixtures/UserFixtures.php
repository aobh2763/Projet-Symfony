<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use App\Entity\{User, Cart, Wishlist};

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ){}

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();

            $user->setUsername("user$i")
                ->setEmail("user$i@example.com")
                ->setFirstName("FirstName$i")
                ->setLastName("LastName$i")
                ->setIsVerified(true)
                ->setPassword($this->passwordHasher->hashPassword($user, "password123"))
            ;

            $cart = new Cart();
            $user->setCart($cart);

            $wishlist = new Wishlist();
            $user->setWishlist($wishlist);

            $manager->persist($user);
        }

        $admin = new User();
        $admin->setUsername('admin')
            ->setEmail('admin@example.com')
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($user, "adminadmin"))
        ;

        $adminCart = new Cart();
        $admin->setCart($adminCart);

        $adminWishlist = new Wishlist();
        $admin->setWishlist($adminWishlist);

        $manager->persist($admin);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['users'];
    }
}

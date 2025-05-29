<?php

namespace App\DataFixtures;

use App\Entity\Accessory;
use App\Entity\Ammo;
use App\Entity\Gun;
use App\Entity\Melee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       $product1 = new Gun();
        $product1->setName('Pistol')
                 ->setDescription('A standard pistol for self-defense.')
                 ->setPrice(100.0)
                 ->setAccuracy(85)
                 ->setStock(50)
                 ->setWeight(1.2)
                 ->setImage("assets/img/uploads/product1.png")
                 ->setCaliber(9.0)
                 ->setGunRange(50.0)
                 ->setSale(0.1);

        $product2 = new Gun();
        $product2->setName('Hunter Rifle')
                 ->setDescription('A high-precision rifle for long-range shooting.')
                 ->setPrice(300.0)
                 ->setAccuracy(95)
                 ->setStock(30)
                 ->setWeight(3.5)
                 ->setImage("assets/img/uploads/product2.png")
                 ->setCaliber(5.56)
                 ->setGunRange(500.0);

        $product3 = new Gun();
        $product3->setName('Sniper Rifle')
                 ->setDescription('A powerful sniper rifle for elite marksmen.')
                 ->setPrice(1500.0)
                 ->setAccuracy(98)
                 ->setStock(10)
                 ->setWeight(4.5)
                 ->setImage("assets/img/uploads/product3.png")
                 ->setCaliber(7.62)
                 ->setGunRange(1000.0)
                 ->setSale(0.2);
        
        $product4 = new Gun();
        $product4->setName('AK-47')
                 ->setDescription('A reliable and robust assault rifle.')
                 ->setPrice(400.0)
                 ->setAccuracy(80)
                 ->setStock(20)
                 ->setWeight(3.0)
                 ->setImage("assets/img/uploads/product4.png")
                 ->setCaliber(7.62)
                 ->setGunRange(300.0);
        
        $product5 = new Ammo();
        $product5->setName('9mm Ammo')
                 ->setDescription('Standard 9mm ammunition for pistols.')
                 ->setPrice(20.0)
                 ->setQuantity(50)
                 ->setStock(100)
                 ->setImage("assets/img/uploads/product7.png")
                 ->setWeight(0.5)
                 ->setGun($product1);
        $product1->setAmmo($product5);

        $product6 = new Ammo();
        $product6->setName('5.56 Ammo')
                 ->setDescription('High-precision 5.56 ammunition for rifles.')
                 ->setPrice(30.0)
                 ->setQuantity(30)
                 ->setStock(60)
                 ->setImage("assets/img/uploads/product6.png")
                 ->setWeight(0.7)
                 ->setGun($product2);
        $product2->setAmmo($product6);

        $product7 = new Ammo();
        $product7->setName('7.62 Ammo')
                 ->setDescription('Powerful 7.62 ammunition for sniper rifles.')
                 ->setPrice(50.0)
                 ->setQuantity(20)
                 ->setStock(40)
                 ->setImage("assets/img/uploads/product8.png")
                 ->setWeight(1.0)
                 ->setGun($product3)
                 ->setSale(0.15);
        $product3->setAmmo($product7);

        $product8 = new Ammo();
        $product8->setName('7.62 AK-47 Ammo')
                 ->setDescription('Standard 7.62 ammunition for AK-47.')
                 ->setPrice(25.0)
                 ->setQuantity(40)
                 ->setStock(80)
                 ->setImage("assets/img/uploads/product5.png")
                 ->setWeight(0.8)
                 ->setGun($product4);
        $product4->setAmmo($product8);

        $product9 = new Melee();
        $product9->setName('Combat Knife')
                 ->setDescription('A sharp and durable combat knife.')
                 ->setPrice(50.0)
                 ->setReach(0.3)
                 ->setType('knife')
                 ->setStock(100)
                 ->setWeight(0.5)
                 ->setImage("assets/img/uploads/product9.png")
                 ->setSale(0.05);

        $product10 = new Melee();
        $product10->setName('Spear')
                  ->setDescription('A long-range melee weapon for thrusting attacks.')
                  ->setPrice(80.0)
                  ->setReach(1.5)
                  ->setType('spear')
                  ->setStock(50)
                  ->setWeight(2.0)
                  ->setImage("assets/img/uploads/product10.png");
        
        $product11 = new Melee();
        $product11->setName('Battle Axe')
                  ->setDescription('A heavy axe for powerful melee attacks.')
                  ->setPrice(120.0)
                  ->setReach(1.2)
                  ->setType('axe')
                  ->setStock(30)
                  ->setWeight(3.0)
                  ->setImage("assets/img/uploads/product11.png")
                  ->setSale(0.3);

        $product12 = new Melee();
        $product12->setName('Baton')
                  ->setDescription('A sturdy baton for crowd control.')
                  ->setPrice(40.0)
                  ->setReach(0.8)
                  ->setType('baton')
                  ->setStock(80)
                  ->setWeight(1.0)
                  ->setImage("assets/img/uploads/product12.png");

        $product13 = new Accessory();
        $product13->setName('Frag Grenade')
                  ->setDescription('A standard fragmentation grenade for explosive attacks.')
                  ->setPrice(60.0)
                  ->setStock(50)
                  ->setWeight(0.3)
                  ->setImage("assets/img/uploads/product13.png")
                  ->setType('grenade');
        
        $product14 = new Accessory();
        $product14->setName('Tactical Shield')
                  ->setDescription('A heavy-duty shield for protection in combat.')
                  ->setPrice(200.0)
                  ->setStock(20)
                  ->setWeight(5.0)
                  ->setImage("assets/img/uploads/product14.png")
                  ->setType('shield');

        $product15 = new Accessory();
        $product15->setName('First Aid Kit')
                  ->setDescription('A comprehensive first aid kit for medical emergencies.')
                  ->setPrice(85.0)
                  ->setStock(100)
                  ->setWeight(1.5)
                  ->setImage("assets/img/uploads/product15.png")
                  ->setType('medkit')
                  ->setSale(0.35);


        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->persist($product5);
        $manager->persist($product6);
        $manager->persist($product7);
        $manager->persist($product8);
        $manager->persist($product9);
        $manager->persist($product10);
        $manager->persist($product11);
        $manager->persist($product12);
        $manager->persist($product13);
        $manager->persist($product14);
        $manager->persist($product15);

        $kukri = new Melee();
        $kukri->setName('Kukri Knife')
            ->setDescription('A traditional Nepalese blade known for its sharp curve.')
            ->setPrice(90.0)
            ->setReach(0.4)
            ->setType('knife')
            ->setStock(40)
            ->setWeight(0.7)
            ->setImage("assets/img/uploads/kurkiknife.webp");

        $nvgs = new Accessory();
        $nvgs->setName('Night Vision Goggles')
            ->setDescription('Optical devices that enhance visibility in low-light conditions.')
            ->setPrice(1200.0)
            ->setStock(10)
            ->setWeight(1.3)
            ->setImage("assets/img/uploads/nightvision.png")
            ->setType('gadget');

        $manager->persist($kukri);
        $manager->persist($nvgs);

        $manager->flush();
    }
}

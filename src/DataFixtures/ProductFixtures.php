<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use App\Entity\{Accessory, Ammo, Gun, Melee};

class ProductFixtures extends Fixture implements FixtureGroupInterface
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
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);

        $product2 = new Gun();
        $product2->setName('Hunter Rifle')
                 ->setDescription('A high-precision rifle for long-range shooting.')
                 ->setPrice(300.0)
                 ->setAccuracy(95)
                 ->setStock(30)
                 ->setWeight(3.5)
                 ->setImage("assets/img/uploads/product2.png")
                 ->setCaliber(5.56)
                 ->setGunRange(500.0)
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);

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
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);
        
        $product4 = new Gun();
        $product4->setName('AK-47')
                 ->setDescription('A reliable and robust assault rifle.')
                 ->setPrice(400.0)
                 ->setAccuracy(80)
                 ->setStock(20)
                 ->setWeight(3.0)
                 ->setImage("assets/img/uploads/product4.png")
                 ->setCaliber(7.62)
                 ->setGunRange(300.0)
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);
        
        $product5 = new Ammo();
        $product5->setName('9mm Ammo')
                 ->setDescription('Standard 9mm ammunition for pistols.')
                 ->setPrice(20.0)
                 ->setQuantity(50)
                 ->setStock(100)
                 ->setImage("assets/img/uploads/product7.png")
                 ->setWeight(0.5)
                 ->setGun($product1)
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);
        $product1->setAmmo($product5);

        $product6 = new Ammo();
        $product6->setName('5.56 Ammo')
                 ->setDescription('High-precision 5.56 ammunition for rifles.')
                 ->setPrice(30.0)
                 ->setQuantity(30)
                 ->setStock(60)
                 ->setImage("assets/img/uploads/product6.png")
                 ->setWeight(0.7)
                 ->setGun($product2)
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);
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
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);
        $product3->setAmmo($product7);

        $product8 = new Ammo();
        $product8->setName('7.62 AK-47 Ammo')
                 ->setDescription('Standard 7.62 ammunition for AK-47.')
                 ->setPrice(25.0)
                 ->setQuantity(40)
                 ->setStock(80)
                 ->setImage("assets/img/uploads/product5.png")
                 ->setWeight(0.8)
                 ->setGun($product4)
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);
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
                 ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                 ->setRating(mt_rand(0, 50) / 10);

        $product10 = new Melee();
        $product10->setName('Spear')
                  ->setDescription('A long-range melee weapon for thrusting attacks.')
                  ->setPrice(80.0)
                  ->setReach(1.5)
                  ->setType('spear')
                  ->setStock(50)
                  ->setWeight(2.0)
                  ->setImage("assets/img/uploads/product10.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product11 = new Melee();
        $product11->setName('Axe')
                  ->setDescription('An axe for powerful melee attacks.')
                  ->setPrice(120.0)
                  ->setReach(1.2)
                  ->setType('axe')
                  ->setStock(30)
                  ->setWeight(3.0)
                  ->setImage("assets/img/uploads/product11.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product12 = new Melee();
        $product12->setName('Baton')
                  ->setDescription('A sturdy baton for crowd control.')
                  ->setPrice(40.0)
                  ->setReach(0.8)
                  ->setType('baton')
                  ->setStock(80)
                  ->setWeight(1.0)
                  ->setImage("assets/img/uploads/product12.png")
                  ->setRating(mt_rand(0, 50) / 10);

        $product13 = new Accessory();
        $product13->setName('Frag Grenade')
                  ->setDescription('A standard fragmentation grenade for explosive attacks.')
                  ->setPrice(60.0)
                  ->setStock(50)
                  ->setWeight(0.3)
                  ->setImage("assets/img/uploads/product13.png")
                  ->setType('grenade')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product14 = new Accessory();
        $product14->setName('Tactical Shield')
                  ->setDescription('A heavy-duty shield for protection in combat.')
                  ->setPrice(200.0)
                  ->setStock(20)
                  ->setWeight(5.0)
                  ->setImage("assets/img/uploads/product14.png")
                  ->setType('shield')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product15 = new Accessory();
        $product15->setName('First Aid Kit')
                  ->setDescription('A comprehensive first aid kit for medical emergencies.')
                  ->setPrice(85.0)
                  ->setStock(100)
                  ->setWeight(1.5)
                  ->setImage("assets/img/uploads/product15.png")
                  ->setType('medkit')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product16 = new Accessory();
        $product16->setName('Night Vision Goggles')
                  ->setDescription('Advanced goggles for low-light visibility.')
                  ->setPrice(250.0)
                  ->setStock(15)
                  ->setWeight(0.8)
                  ->setImage("assets/img/uploads/nightvision.png")
                  ->setType('goggles')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product17 = new Gun();
        $product17->setName('Crossbow')
                  ->setDescription('A silent and deadly crossbow for stealth attacks.')
                  ->setPrice(600.0)
                  ->setAccuracy(90)
                  ->setStock(25)
                  ->setWeight(2.5)
                  ->setImage("assets/img/uploads/crossbow.png")
                  ->setCaliber(0.0)
                  ->setGunRange(200.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product18 = new Gun();
        $product18->setName('Flamethrower')
                  ->setDescription('A powerful flamethrower for area denial.')
                  ->setPrice(1200.0)
                  ->setAccuracy(70)
                  ->setStock(5)
                  ->setWeight(10.0)
                  ->setImage("assets/img/uploads/flamethrower.png")
                  ->setCaliber(0.0)
                  ->setGunRange(100.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product19 = new Gun();
        $product19->setName('Golden Pistol')
                  ->setDescription('A luxurious golden pistol with high prestige.')
                  ->setPrice(5000.0)
                  ->setAccuracy(90)
                  ->setStock(2)
                  ->setWeight(1.5)
                  ->setImage("assets/img/uploads/goldenpistol.png")
                  ->setCaliber(9.0)
                  ->setGunRange(50.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product20 = new Gun();
        $product20->setName('Handgun')
                  ->setDescription('A compact handgun for easy concealment.')
                  ->setPrice(200.0)
                  ->setAccuracy(80)
                  ->setStock(40)
                  ->setWeight(1.0)
                  ->setImage("assets/img/uploads/handgun.png")
                  ->setCaliber(9.0)
                  ->setGunRange(30.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product21 = new Gun();
        $product21->setName('Laser Gun')
                  ->setDescription('A futuristic laser gun with high-tech features.')
                  ->setPrice(3000.0)
                  ->setAccuracy(95)
                  ->setStock(10)
                  ->setWeight(2.0)
                  ->setImage("assets/img/uploads/lasergun.png")
                  ->setCaliber(0.0)
                  ->setGunRange(200.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product22 = new Gun();
        $product22->setName('M4 Carbine')
                  ->setDescription('A versatile M4 carbine for military use.')
                  ->setPrice(800.0)
                  ->setAccuracy(85)
                  ->setStock(15)
                  ->setWeight(3.2)
                  ->setImage("assets/img/uploads/m4carbine.png")
                  ->setCaliber(5.56)
                  ->setGunRange(400.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product23 = new Gun();
        $product23->setName('MP7')
                  ->setDescription('A compact submachine gun for close-quarters combat.')
                  ->setPrice(700.0)
                  ->setAccuracy(80)
                  ->setStock(20)
                  ->setWeight(2.5)
                  ->setImage("assets/img/uploads/mp7.png")
                  ->setCaliber(4.6)
                  ->setGunRange(100.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product24 = new Gun();
        $product24->setName('Rocket Launcher')
                  ->setDescription('A heavy-duty rocket launcher for explosive firepower.')
                  ->setPrice(2000.0)
                  ->setAccuracy(70)
                  ->setStock(5)
                  ->setWeight(15.0)
                  ->setImage("assets/img/uploads/rocketlauncher.png")
                  ->setCaliber(0.0)
                  ->setGunRange(300.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product25 = new Gun();
        $product25->setName('AR14')
                  ->setDescription('A modern AR14 rifle with advanced features.')
                  ->setPrice(900.0)
                  ->setAccuracy(90)
                  ->setStock(12)
                  ->setWeight(3.8)
                  ->setImage("assets/img/uploads/ar14.png")
                  ->setCaliber(5.56)
                  ->setGunRange(450.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product26 = new Gun();
        $product26->setName('CLMG 556 Machine Gun')
                  ->setDescription('A powerful CLMG 556 machine gun for heavy fire support.')
                  ->setPrice(2500.0)
                  ->setAccuracy(75)
                  ->setStock(8)
                  ->setWeight(10.0)
                  ->setImage("assets/img/uploads/clmg556.png")
                  ->setCaliber(5.56)
                  ->setGunRange(600.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product27 = new Ammo();
        $product27->setName('Browning Ammo')
                  ->setDescription('High-quality Browning ammunition for various firearms.')
                  ->setPrice(35.0)
                  ->setQuantity(25)
                  ->setStock(70)
                  ->setImage("assets/img/uploads/browningammo.png")
                  ->setWeight(0.6)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product25->setAmmo($product27);

        $product28 = new Ammo();
        $product28->setName('Flamethrower Fuel')
                  ->setDescription('Special fuel for flamethrowers.')
                  ->setPrice(100.0)
                  ->setQuantity(10)
                  ->setStock(20)
                  ->setImage("assets/img/uploads/flamethrowerfuel.png")
                  ->setWeight(2.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product18->setAmmo($product28);

        $product29 = new Ammo();
        $product29->setName('Golden Bullets')
                  ->setDescription('Luxurious golden bullets for high-end firearms.')
                  ->setPrice(500.0)
                  ->setQuantity(5)
                  ->setStock(10)
                  ->setImage("assets/img/uploads/goldenbullets.png")
                  ->setWeight(0.3)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product19->setAmmo($product29);

        $product30 = new Ammo();
        $product30->setName('HSM Ammo')
                  ->setDescription('High-quality HSM ammunition for various firearms.')
                  ->setPrice(40.0)
                  ->setQuantity(20)
                  ->setStock(60)
                  ->setImage("assets/img/uploads/hsmammo.png")
                  ->setWeight(0.5)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product20->setAmmo($product30);

        $product31 = new Ammo();
        $product31->setName('Magtech Ammo')
                  ->setDescription('Reliable Magtech ammunition for various firearms.')
                  ->setPrice(30.0)
                  ->setQuantity(30)
                  ->setStock(80)
                  ->setImage("assets/img/uploads/magtechammo.png")
                  ->setWeight(0.4)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product21->setAmmo($product31);

        $product32 = new Ammo();
        $product32->setName('Nielsen Ammo')
                  ->setDescription('High-performance Nielsen ammunition for various firearms.')
                  ->setPrice(45.0)
                  ->setQuantity(15)
                  ->setStock(50)
                  ->setImage("assets/img/uploads/nielsenammo.png")
                  ->setWeight(0.5)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product22->setAmmo($product32);

        $product33 = new Ammo();
        $product33->setName('NXO Ammo')
                  ->setDescription('NXO ammunition for high-performance firearms.')
                  ->setPrice(55.0)
                  ->setQuantity(10)
                  ->setStock(30)
                  ->setImage("assets/img/uploads/nxoammo.png")
                  ->setWeight(0.6)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product23->setAmmo($product33);

        $product34 = new Ammo();
        $product34->setName('Rifle Line Ammo')
                  ->setDescription('Rifle Line ammunition for various rifles.')
                  ->setPrice(65.0)
                  ->setQuantity(12)
                  ->setStock(25)
                  ->setImage("assets/img/uploads/riflelineammo.png")
                  ->setWeight(0.7)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product26->setAmmo($product34);

        $product35 = new Ammo();
        $product35->setName('Rockets')
                  ->setDescription('Rockets for heavy-duty rocket launchers.')
                  ->setPrice(300.0)
                  ->setQuantity(3)
                  ->setStock(10)
                  ->setImage("assets/img/uploads/rockets.png")
                  ->setWeight(3.0)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product24->setAmmo($product35);

        $product36 = new Ammo();
        $product36->setName('Crossbow Arrows')
                  ->setDescription('Specialized arrows for crossbows.')
                  ->setPrice(15.0)
                  ->setQuantity(6)
                  ->setStock(50)
                  ->setImage("assets/img/uploads/crossbowarrows.png")
                  ->setWeight(0.4)
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        $product17->setAmmo($product36);

        $product37 = new Melee();
        $product37->setName('Battle Axe')
                  ->setDescription('A heavy axe for powerful melee attacks.')
                  ->setPrice(120.0)
                  ->setReach(1.2)
                  ->setType('axe')
                  ->setStock(30)
                  ->setWeight(3.0)
                  ->setImage("assets/img/uploads/battleaxe.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product38 = new Melee();
        $product38->setName('Dagger')
                  ->setDescription('A small and sharp dagger for stealth attacks.')
                  ->setPrice(30.0)
                  ->setReach(0.2)
                  ->setType('dagger')
                  ->setStock(100)
                  ->setWeight(0.3)
                  ->setImage("assets/img/uploads/dagger.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product39 = new Melee();
        $product39->setName('Katana')
                  ->setDescription('A traditional Japanese sword with a sharp blade.')
                  ->setPrice(200.0)
                  ->setReach(1.0)
                  ->setType('sword')
                  ->setStock(20)
                  ->setWeight(1.5)
                  ->setImage("assets/img/uploads/katana.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product40 = new Melee();
        $product40->setName('Lightsaber')
                  ->setDescription('A futuristic lightsaber with a glowing blade.')
                  ->setPrice(1500.0)
                  ->setReach(1.5)
                  ->setType('lightsaber')
                  ->setStock(5)
                  ->setWeight(1.0)
                  ->setImage("assets/img/uploads/lightsaber.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product41 = new Melee();
        $product41->setName('Mace')
                  ->setDescription('A heavy mace for crushing attacks.')
                  ->setPrice(90.0)
                  ->setReach(1.0)
                  ->setType('mace')
                  ->setStock(40)
                  ->setWeight(2.0)
                  ->setImage("assets/img/uploads/mace.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product42 = new Melee();
        $product42->setName('Nunchucks')
                  ->setDescription('A pair of traditional martial arts nunchucks.')
                  ->setPrice(70.0)
                  ->setReach(0.5)
                  ->setType('nunchucks')
                  ->setStock(60)
                  ->setWeight(0.8)
                  ->setImage("assets/img/uploads/nunchucks.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product43 = new Melee();
        $product43->setName('Rapier')
                  ->setDescription('A long and slender sword for precise thrusting attacks.')
                  ->setPrice(250.0)
                  ->setReach(1.2)
                  ->setType('rapier')
                  ->setStock(15)
                  ->setWeight(1.2)
                  ->setImage("assets/img/uploads/rapier.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product44 = new Melee();
        $product44->setName('Scythe')
                  ->setDescription('A curved blade for sweeping attacks.')
                  ->setPrice(180.0)
                  ->setReach(1.5)
                  ->setType('scythe')
                  ->setStock(10)
                  ->setWeight(2.5)
                  ->setImage("assets/img/uploads/scythe.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product45 = new Melee();
        $product45->setName('Sickle')
                  ->setDescription('A curved blade for cutting attacks.')
                  ->setPrice(60.0)
                  ->setReach(0.8)
                  ->setType('sickle')
                  ->setStock(50)
                  ->setWeight(0.6)
                  ->setImage("assets/img/uploads/sickle.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product46 = new Melee();
        $product46->setName('Trident')
                  ->setDescription('A three-pronged spear for thrusting attacks.')
                  ->setPrice(100.0)
                  ->setReach(1.5)
                  ->setType('trident')
                  ->setStock(20)
                  ->setWeight(1.8)
                  ->setImage("assets/img/uploads/trident.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product47 = new Accessory();
        $product47->setName('Bandage')
                  ->setDescription('A simple bandage for minor injuries.')
                  ->setPrice(10.0)
                  ->setStock(200)
                  ->setWeight(0.1)
                  ->setImage("assets/img/uploads/bandage.png")
                  ->setType('bandage')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product48 = new Accessory();
        $product48->setName('Gas Mask')
                  ->setDescription('A protective gas mask for hazardous environments.')
                  ->setPrice(150.0)
                  ->setStock(30)
                  ->setWeight(0.5)
                  ->setImage("assets/img/uploads/gasmask.png")
                  ->setType('mask')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product49 = new Accessory();
        $product49->setName('Bulletproof Vest')
                  ->setDescription('A lightweight bulletproof vest for protection.')
                  ->setPrice(300.0)
                  ->setStock(25)
                  ->setWeight(2.0)
                  ->setImage("assets/img/uploads/bulletproofvest.png")
                  ->setType('vest')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product50 = new Accessory();
        $product50->setName('Pepper Spray')
                  ->setDescription('A canister of pepper spray for self-defense.')
                  ->setPrice(20.0)
                  ->setStock(150)
                  ->setWeight(0.2)
                  ->setImage("assets/img/uploads/pepperspray.png")
                  ->setType('spray')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product51 = new Accessory();
        $product51->setName('Scope')
                  ->setDescription('A high-precision scope for firearms.')
                  ->setPrice(300.0)
                  ->setStock(25)
                  ->setWeight(0.4)
                  ->setImage("assets/img/uploads/scope.png")
                  ->setType('scope')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product52 = new Accessory();
        $product52->setName('Silencer')
                  ->setDescription('A silencer for firearms to reduce noise.')
                  ->setPrice(200.0)
                  ->setStock(40)
                  ->setWeight(0.3)
                  ->setImage("assets/img/uploads/silencer.png")
                  ->setType('silencer')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product53 = new Melee();
        $product53->setName('Kurki Knife')
                  ->setDescription('A traditional Kurki knife with a curved blade.')
                  ->setPrice(80.0)
                  ->setReach(0.5)
                  ->setType('knife')
                  ->setStock(50)
                  ->setWeight(0.4)
                  ->setImage("assets/img/uploads/kurkiknife.webp")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product54 = new Melee();
        $product54->setName('Baseball Bat')
                  ->setDescription('A wooden baseball bat for blunt force attacks.')
                  ->setPrice(40.0)
                  ->setReach(1.0)
                  ->setType('bat')
                  ->setStock(100)
                  ->setWeight(1.2)
                  ->setImage("assets/img/uploads/baseballbat.png")
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product55 = new Accessory();
        $product55->setName('Flashbang Grenade')
                  ->setDescription('A flashbang grenade for disorienting enemies.')
                  ->setPrice(100.0)
                  ->setStock(20)
                  ->setWeight(0.5)
                  ->setImage("assets/img/uploads/flashbanggrenade.png")
                  ->setType('grenade')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product56 = new Accessory();
        $product56->setName('Incendiary Grenade')
                  ->setDescription('An incendiary grenade for causing fire damage.')
                  ->setPrice(150.0)
                  ->setStock(15)
                  ->setWeight(0.6)
                  ->setImage("assets/img/uploads/incendiarygrenade.png")
                  ->setType('grenade')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);
        
        $product57 = new Accessory();
        $product57->setName('Shooting Glasses')
                  ->setDescription('Protective shooting glasses for eye safety.')
                  ->setPrice(25.0)
                  ->setStock(100)
                  ->setWeight(0.2)
                  ->setImage("assets/img/uploads/shootingglasses.png")
                  ->setType('glasses')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product58 = new Accessory();
        $product58->setName('Shooting Gloves')
                  ->setDescription('Protective gloves for shooting sports.')
                  ->setPrice(30.0)
                  ->setStock(80)
                  ->setWeight(0.3)
                  ->setImage("assets/img/uploads/shootingglove.png")
                  ->setType('gloves')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product59 = new Accessory();
        $product59->setName('Shuriken')
                  ->setDescription('A traditional Japanese throwing star.')
                  ->setPrice(15.0)
                  ->setStock(200)
                  ->setWeight(0.1)
                  ->setImage("assets/img/uploads/shuriken.png")
                  ->setType('throwing_star')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

        $product60 = new Accessory();
        $product60->setName('Tactical Helmet')
                  ->setDescription('A tactical helmet for head protection.')
                  ->setPrice(200.0)
                  ->setStock(30)
                  ->setWeight(1.5)
                  ->setImage("assets/img/uploads/tacticalhelmet.png")
                  ->setType('helmet')
                  ->setSale((mt_rand(1, 100) <= 75) ? 0.0 : [0.1, 0.2, 0.3, 0.4, 0.5][array_rand([0.1, 0.2, 0.3, 0.4, 0.5])])
                  ->setRating(mt_rand(0, 50) / 10);

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
        $manager->persist($product16);
        $manager->persist($product17);
        $manager->persist($product18);
        $manager->persist($product19);
        $manager->persist($product20);
        $manager->persist($product21);
        $manager->persist($product22);
        $manager->persist($product23);
        $manager->persist($product24);
        $manager->persist($product25);
        $manager->persist($product26);
        $manager->persist($product27);
        $manager->persist($product28);
        $manager->persist($product29);
        $manager->persist($product30);
        $manager->persist($product31);
        $manager->persist($product32);
        $manager->persist($product33);
        $manager->persist($product34);
        $manager->persist($product35);
        $manager->persist($product36);
        $manager->persist($product37);
        $manager->persist($product38);
        $manager->persist($product39);
        $manager->persist($product40);
        $manager->persist($product41);
        $manager->persist($product42);
        $manager->persist($product43);
        $manager->persist($product44);
        $manager->persist($product45);
        $manager->persist($product46);
        $manager->persist($product47);
        $manager->persist($product48);
        $manager->persist($product49);
        $manager->persist($product50);
        $manager->persist($product51);
        $manager->persist($product52);
        $manager->persist($product53);
        $manager->persist($product54);
        $manager->persist($product55);
        $manager->persist($product56);
        $manager->persist($product57);
        $manager->persist($product58);
        $manager->persist($product59);
        $manager->persist($product60);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['products'];
    }
}

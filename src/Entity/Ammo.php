<?php

namespace App\Entity;

use App\Repository\AmmoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AmmoRepository::class)]
class Ammo extends Product
{

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\OneToOne(inversedBy: 'ammo', cascade: ['persist', 'remove'])]
    private ?Gun $gun = null;

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getGun(): ?Gun
    {
        return $this->gun;
    }

    public function setGun(?Gun $gun): static
    {
        $this->gun = $gun;

        return $this;
    }

    public function getCategory(): string
    {
        return 'ammo';
    }
}

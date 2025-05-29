<?php

namespace App\Entity;

use App\Repository\GunRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GunRepository::class)]
class Gun extends Product
{

    #[ORM\Column]
    private ?float $accuracy = null;

    #[ORM\Column]
    private ?float $caliber = null;

    #[ORM\Column]
    private ?float $gun_range = null;

    #[ORM\OneToOne(mappedBy: 'gun', cascade: ['persist', 'remove'])]
    private ?Ammo $ammo = null;

    public function getAccuracy(): ?float
    {
        return $this->accuracy;
    }

    public function setAccuracy(float $accuracy): static
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    public function getCaliber(): ?float
    {
        return $this->caliber;
    }

    public function setCaliber(float $caliber): static
    {
        $this->caliber = $caliber;

        return $this;
    }

    public function getGunRange(): ?float
    {
        return $this->gun_range;
    }

    public function setGunRange(float $gun_range): static
    {
        $this->gun_range = $gun_range;

        return $this;
    }

    public function getAmmo(): ?Ammo
    {
        return $this->ammo;
    }

    public function setAmmo(?Ammo $ammo): static
    {
        // unset the owning side of the relation if necessary
        if ($ammo === null && $this->ammo !== null) {
            $this->ammo->setGun(null);
        }

        // set the owning side of the relation if necessary
        if ($ammo !== null && $ammo->getGun() !== $this) {
            $ammo->setGun($this);
        }

        $this->ammo = $ammo;

        return $this;
    }

    public function getCategory(): string
    {
        return 'gun';
    }
}

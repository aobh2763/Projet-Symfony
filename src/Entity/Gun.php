<?php

namespace App\Entity;

use App\Repository\GunRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GunRepository::class)]
class Gun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?float $accuracy = null;
    #[ORM\Column]
    private ?float $caliber = null;

    #[ORM\Column]
    private ?float $rang = null;

    #[ORM\OneToOne(targetEntity: Ammo::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Ammo $ammo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccuracy(): ?float
    {
        return $this->caliber;
    }

    public function setAccuracy(float $accrucy): static
    {
        $this->$accrucy = $accrucy;

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

    public function getRang(): ?float
    {
        return $this->rang;
    }

    public function setRang(float $rang): static
    {
        $this->rang = $rang;

        return $this;
    }
}

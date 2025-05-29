<?php

namespace App\Entity;

use App\Repository\MeleeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeleeRepository::class)]
class Melee extends Product
{

    #[ORM\Column]
    private ?float $reach = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getReach(): ?float
    {
        return $this->reach;
    }

    public function setReach(float $reach): static
    {
        $this->reach = $reach;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): string
    {
        return 'melee';
    }
}

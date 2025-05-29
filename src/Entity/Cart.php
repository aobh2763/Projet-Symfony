<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $prixtotal = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'cart', cascade: ['persist'])]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'cart', cascade: ['persist', 'remove'])]
    private ?User $user = null;


    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->prixtotal = 0.0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixtotal(): ?float
    {
        return $this->prixtotal;
    }

    public function setPrixtotal(float $prixtotal): static
    {
        $this->prixtotal = $prixtotal;

        return $this;
    }

    public function updatePrixTotal(): static {
        $total = 0.0;
        foreach ($this->orders as $order) {
            $total += ($order->getProduct()->getPrice() - $order->getProduct()->getPrice() * $order->getProduct()->getSale()) * $order->getQuantity();
        }
        $this->setPrixtotal($total);

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCart($this);
        }
        $this->updatePrixTotal();

        return $this;
    }

    public function removeOrder(Order $order): static {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCart() === $this) {
                $order->setCart(null);
            }
        }

        $this->updatePrixTotal();

        return $this;
    }

    public function clearOrders(): static {
        foreach ($this->orders as $order) {
            $order->setCart(null);
        }
        $this->orders->clear();

        $this->setPrixtotal(0.0);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        // set the owning side of the relation if necessary
        if ($user->getCart() !== $this) {
            $user->setCart($this);
        }

        $this->user = $user;

        return $this;
    }


}

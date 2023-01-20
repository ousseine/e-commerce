<?php

namespace App\Entity;

use App\Repository\OrderQuantityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderQuantityRepository::class)]
class OrderQuantity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\OneToMany(mappedBy: 'orderQuantity', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $product;

    #[ORM\OneToMany(mappedBy: 'orderQuantity', targetEntity: Order::class)]
    private Collection $fromOrder;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->fromOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
            $product->setOrderQuantity($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getOrderQuantity() === $this) {
                $product->setOrderQuantity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getFromOrder(): Collection
    {
        return $this->fromOrder;
    }

    public function addFromOrder(Order $fromOrder): self
    {
        if (!$this->fromOrder->contains($fromOrder)) {
            $this->fromOrder->add($fromOrder);
            $fromOrder->setOrderQuantity($this);
        }

        return $this;
    }

    public function removeFromOrder(Order $fromOrder): self
    {
        if ($this->fromOrder->removeElement($fromOrder)) {
            // set the owning side to null (unless already changed)
            if ($fromOrder->getOrderQuantity() === $this) {
                $fromOrder->setOrderQuantity(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->quantity;
    }
}

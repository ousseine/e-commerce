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

    #[ORM\ManyToOne(inversedBy: 'orderQuantities')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderQuantities')]
    private ?Order $fromOrder = null;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getFromOrder(): ?Order
    {
        return $this->fromOrder;
    }

    public function setFromOrder(?Order $fromOrder): self
    {
        $this->fromOrder = $fromOrder;

        return $this;
    }
}

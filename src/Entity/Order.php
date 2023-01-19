<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    #[ORM\OneToOne(mappedBy: 'fromOrder', cascade: ['persist', 'remove'])]
    private ?PaymentRequest $paymentRequest = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setOrders($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getOrders() === $this) {
                $product->setOrders(null);
            }
        }

        return $this;
    }

    public function getPaymentRequest(): ?PaymentRequest
    {
        return $this->paymentRequest;
    }

    public function setPaymentRequest(?PaymentRequest $paymentRequest): self
    {
        // unset the owning side of the relation if necessary
        if ($paymentRequest === null && $this->paymentRequest !== null) {
            $this->paymentRequest->setFromOrder(null);
        }

        // set the owning side of the relation if necessary
        if ($paymentRequest !== null && $paymentRequest->getFromOrder() !== $this) {
            $paymentRequest->setFromOrder($this);
        }

        $this->paymentRequest = $paymentRequest;

        return $this;
    }
}

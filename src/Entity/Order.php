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

    #[ORM\OneToOne(mappedBy: 'fromOrder', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?PaymentRequest $paymentRequest = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_send = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $send_at = null;

    #[ORM\OneToMany(mappedBy: 'fromOrder', targetEntity: OrderQuantity::class, orphanRemoval: true)]
    private Collection $orderQuantities;

    public function __construct()
    {
        $this->orderQuantities = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isIsSend(): ?bool
    {
        return $this->is_send;
    }

    public function setIsSend(?bool $is_send): self
    {
        $this->is_send = $is_send;

        return $this;
    }

    public function getSendAt(): ?\DateTimeImmutable
    {
        return $this->send_at;
    }

    public function setSendAt(?\DateTimeImmutable $send_at): self
    {
        $this->send_at = $send_at;

        return $this;
    }

    /**
     * @return Collection<int, OrderQuantity>
     */
    public function getOrderQuantities(): Collection
    {
        return $this->orderQuantities;
    }

    public function addOrderQuantity(OrderQuantity $orderQuantity): self
    {
        if (!$this->orderQuantities->contains($orderQuantity)) {
            $this->orderQuantities->add($orderQuantity);
            $orderQuantity->setFromOrder($this);
        }

        return $this;
    }

    public function removeOrderQuantity(OrderQuantity $orderQuantity): self
    {
        if ($this->orderQuantities->removeElement($orderQuantity)) {
            // set the owning side to null (unless already changed)
            if ($orderQuantity->getFromOrder() === $this) {
                $orderQuantity->setFromOrder(null);
            }
        }

        return $this;
    }
}

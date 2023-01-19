<?php

namespace App\Entity;

use App\Repository\PaymentRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRequestRepository::class)]
class PaymentRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paid_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validated = null;

    #[ORM\OneToOne(inversedBy: 'paymentRequest', cascade: ['persist', 'remove'])]
    private ?Order $fromOrder = null;

    #[ORM\Column(length: 255)]
    private ?string $stripeSessionId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeImmutable $paid_at): self
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(?bool $validated): self
    {
        $this->validated = $validated;

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

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ApiResource]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\OneToOne(inversedBy: 'payment', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderContainer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $paidDate = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getOrderContainer(): ?Order
    {
        return $this->orderContainer;
    }

    public function setOrderContainer(Order $orderContainer): self
    {
        $this->orderContainer = $orderContainer;

        return $this;
    }

    public function getPaidDate(): ?\DateTimeImmutable
    {
        return $this->paidDate;
    }

    public function setPaidDate(\DateTimeImmutable $paidDate): self
    {
        $this->paidDate = $paidDate;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}

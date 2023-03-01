<?php

namespace App\Entity;

use App\Repository\FuelTransactionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FuelTransactionsRepository::class)]
class FuelTransactions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $yard_name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $paid_amount = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $settlement_id = null;

    #[ORM\ManyToOne(inversedBy: 'fuelTransactions')]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYardName(): ?string
    {
        return $this->yard_name;
    }

    public function setYardName(string $yard_name): self
    {
        $this->yard_name = $yard_name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPaidAmount(): ?string
    {
        return $this->paid_amount;
    }

    public function setPaidAmount(string $paid_amount): self
    {
        $this->paid_amount = $paid_amount;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSettlementId(): ?string
    {
        return $this->settlement_id;
    }

    public function setSettlementId(?string $settlement_id): self
    {
        $this->settlement_id = $settlement_id;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}

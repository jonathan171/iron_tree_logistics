<?php

namespace App\Entity;

use App\Repository\LoadsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoadsRepository::class)]
class Loads
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $fuel_surcharge = null;

    #[ORM\Column(length: 6444)]
    private ?string $well_name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $driver_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $arrived_at_loader = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $loaded_distance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $line_haul = null;

    #[ORM\Column(length: 255)]
    private ?string $order_status = null;

    #[ORM\Column(length: 255)]
    private ?string $billing_status = null;

    #[ORM\ManyToOne(inversedBy: 'loads')]
    private ?Trier $trier = null;

    #[ORM\Column(length: 255)]
    private ?string $dispatched_loader = null;

    #[ORM\ManyToOne(inversedBy: 'loads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFuelSurcharge(): ?string
    {
        return $this->fuel_surcharge;
    }

    public function setFuelSurcharge(string $fuel_surcharge): self
    {
        $this->fuel_surcharge = $fuel_surcharge;

        return $this;
    }

    public function getWellName(): ?string
    {
        return $this->well_name;
    }

    public function setWellName(string $well_name): self
    {
        $this->well_name = $well_name;

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

    public function getDriverName(): ?string
    {
        return $this->driver_name;
    }

    public function setDriverName(string $driver_name): self
    {
        $this->driver_name = $driver_name;

        return $this;
    }

    public function getArrivedAtLoader(): ?\DateTimeInterface
    {
        return $this->arrived_at_loader;
    }

    public function setArrivedAtLoader(\DateTimeInterface $arrived_at_loader): self
    {
        $this->arrived_at_loader = $arrived_at_loader;

        return $this;
    }

    public function getLoadedDistance(): ?string
    {
        return $this->loaded_distance;
    }

    public function setLoadedDistance(string $loaded_distance): self
    {
        $this->loaded_distance = $loaded_distance;

        return $this;
    }

    public function getLineHaul(): ?string
    {
        return $this->line_haul;
    }

    public function setLineHaul(string $line_haul): self
    {
        $this->line_haul = $line_haul;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->order_status;
    }

    public function setOrderStatus(string $order_status): self
    {
        $this->order_status = $order_status;

        return $this;
    }

    public function getBillingStatus(): ?string
    {
        return $this->billing_status;
    }

    public function setBillingStatus(string $billing_status): self
    {
        $this->billing_status = $billing_status;

        return $this;
    }

    public function getTrier(): ?Trier
    {
        return $this->trier;
    }

    public function setTrier(?Trier $trier): self
    {
        $this->trier = $trier;

        return $this;
    }

    public function getDispatchedLoader(): ?string
    {
        return $this->dispatched_loader;
    }

    public function setDispatchedLoader(string $dispatched_loader): self
    {
        $this->dispatched_loader = $dispatched_loader;

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

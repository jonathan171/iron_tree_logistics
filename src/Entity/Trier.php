<?php

namespace App\Entity;

use App\Repository\TrierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrierRepository::class)]
class Trier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2)]
    private ?string $percentage = null;

    #[ORM\OneToMany(mappedBy: 'trier', targetEntity: Loads::class)]
    private Collection $loads;

    public function __construct()
    {
        $this->loads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(string $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * @return Collection<int, Loads>
     */
    public function getLoads(): Collection
    {
        return $this->loads;
    }

    public function addLoad(Loads $load): self
    {
        if (!$this->loads->contains($load)) {
            $this->loads->add($load);
            $load->setTrier($this);
        }

        return $this;
    }

    public function removeLoad(Loads $load): self
    {
        if ($this->loads->removeElement($load)) {
            // set the owning side to null (unless already changed)
            if ($load->getTrier() === $this) {
                $load->setTrier(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->description.' ('.$this->percentage.')';
    }
}

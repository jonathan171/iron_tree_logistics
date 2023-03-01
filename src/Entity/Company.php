<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Loads::class)]
    private Collection $loads;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: FuelTransactions::class)]
    private Collection $fuelTransactions;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: UserCompany::class)]
    private Collection $userCompanies;

    public function __construct()
    {
        $this->loads = new ArrayCollection();
        $this->fuelTransactions = new ArrayCollection();
        $this->userCompanies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $load->setCompany($this);
        }

        return $this;
    }

    public function removeLoad(Loads $load): self
    {
        if ($this->loads->removeElement($load)) {
            // set the owning side to null (unless already changed)
            if ($load->getCompany() === $this) {
                $load->setCompany(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return Collection<int, FuelTransactions>
     */
    public function getFuelTransactions(): Collection
    {
        return $this->fuelTransactions;
    }

    public function addFuelTransaction(FuelTransactions $fuelTransaction): self
    {
        if (!$this->fuelTransactions->contains($fuelTransaction)) {
            $this->fuelTransactions->add($fuelTransaction);
            $fuelTransaction->setCompany($this);
        }

        return $this;
    }

    public function removeFuelTransaction(FuelTransactions $fuelTransaction): self
    {
        if ($this->fuelTransactions->removeElement($fuelTransaction)) {
            // set the owning side to null (unless already changed)
            if ($fuelTransaction->getCompany() === $this) {
                $fuelTransaction->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserCompany>
     */
    public function getUserCompanies(): Collection
    {
        return $this->userCompanies;
    }

    public function addUserCompany(UserCompany $userCompany): self
    {
        if (!$this->userCompanies->contains($userCompany)) {
            $this->userCompanies->add($userCompany);
            $userCompany->setCompany($this);
        }

        return $this;
    }

    public function removeUserCompany(UserCompany $userCompany): self
    {
        if ($this->userCompanies->removeElement($userCompany)) {
            // set the owning side to null (unless already changed)
            if ($userCompany->getCompany() === $this) {
                $userCompany->setCompany(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDocumentRepository::class)]
class UserDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cdl = null;

    #[ORM\Column(length: 255)]
    private ?string $medical_card = null;

    #[ORM\Column(length: 255)]
    private ?string $h2s = null;

    #[ORM\Column(length: 255)]
    private ?string $pec = null;

    #[ORM\Column(length: 255)]
    private ?string $cuestionario = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCdl(): ?string
    {
        return $this->cdl;
    }

    public function setCdl(?string $cdl): self
    {
        $this->cdl = $cdl;

        return $this;
    }

    public function getMedicalCard(): ?string
    {
        return $this->medical_card;
    }

    public function setMedicalCard(?string $medical_card): self
    {
        $this->medical_card = $medical_card;

        return $this;
    }

    public function getH2s(): ?string
    {
        return $this->h2s;
    }

    public function setH2s(?string $h2s): self
    {
        $this->h2s = $h2s;

        return $this;
    }

    public function getPec(): ?string
    {
        return $this->pec;
    }

    public function setPec(?string $pec): self
    {
        $this->pec = $pec;

        return $this;
    }

    public function getCuestionario(): ?string
    {
        return $this->cuestionario;
    }

    public function setCuestionario(?string $cuestionario): self
    {
        $this->cuestionario = $cuestionario;

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
}

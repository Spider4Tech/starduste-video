<?php

namespace App\Entity;

use App\Repository\ReportsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportsRepository::class)]
class Reports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Video $idvideo = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Raisons $Raison = null;

    #[ORM\ManyToOne]
    private ?Comments $idcommentaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PrecisionUser = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $idUser = null;

    #[ORM\Column(nullable: true)]
    private ?bool $traite = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $datecreation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdvideo(): ?Video
    {
        return $this->idvideo;
    }

    public function setIdvideo(?Video $idvideo): static
    {
        $this->idvideo = $idvideo;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getRaison(): ?Raisons
    {
        return $this->Raison;
    }

    public function setRaison(?Raisons $Raison): static
    {
        $this->Raison = $Raison;

        return $this;
    }

    public function getIdcommentaire(): ?Comments
    {
        return $this->idcommentaire;
    }

    public function setIdcommentaire(?Comments $idcommentaire): static
    {
        $this->idcommentaire = $idcommentaire;

        return $this;
    }

    public function getPrecisionUser(): ?string
    {
        return $this->PrecisionUser;
    }

    public function setPrecisionUser(?string $PrecisionUser): static
    {
        $this->PrecisionUser = $PrecisionUser;

        return $this;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->idUser;
    }

    public function setIdUser(?Utilisateurs $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function isTraite(): ?bool
    {
        return $this->traite;
    }

    public function setTraite(?bool $traite): static
    {
        $this->traite = $traite;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeImmutable
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeImmutable $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }
}

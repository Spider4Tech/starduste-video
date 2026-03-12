<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormTypeInterface;


#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\Table(name: "Utilisateurs")]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36, unique: true)]
    private ?string $uuid = null;




    #[ORM\Column(length: 24)]
    #[Assert\Range(max: 24, maxMessage: "veuillez entrer un pseudo de moins de 24 lettres")]
    #[Assert\NotBlank(message: "veuillez entrer un pseudo")]
    private ?string $Pseudo = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $SUBSCRIBERS = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $JOIN_DATE = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $UPLOADED_VIDEO = null;

    #[ORM\Column(nullable: true)]
    private ?bool $IS_ADMIN = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 13, max: 120, notInRangeMessage: "veuillez entrer un age entre 13 et 120 ans")]
    private ?int $AGE = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 64)]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).+$/",
        message: "Le mot de passe doit contenir au moins une lettre un chiffre et un symbole."
    )]
    private ?string $PASSWORD = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\Email]
    #[Assert\Length(max : 50)]
    #[Assert\NotBlank]
    private ?string $EMAIL = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Ip]
    private ?string $IP_ADRESSE = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $LAST_LOGIN = null;

    #[ORM\Column(nullable:true)]
    private ?string $pfppath = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    } public function getId(): ?int
    {
        return $this->id;
    }

    public function getPfppath(): ?string
    {
        return $this->pfppath;
    }

    public function setPfppath(?string $pfppath): void
    {
        $this->pfppath = $pfppath;
    }

    public function getPseudo(): ?string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $Pseudo): static
    {
        $this->Pseudo = $Pseudo;

        return $this;
    }

    public function getSUBSCRIBERS(): ?int
    {
        return $this->SUBSCRIBERS;
    }

    public function setSUBSCRIBERS(int $SUBSCRIBERS): static
    {
        $this->SUBSCRIBERS = $SUBSCRIBERS;

        return $this;
    }

    public function getJOINDATE(): ?\DateTime
    {
        return $this->JOIN_DATE;
    }

    public function setJOINDATE(?\DateTime $JOIN_DATE): static
    {
        $this->JOIN_DATE = $JOIN_DATE;

        return $this;
    }

    public function getUPLOADEDVIDEO(): ?int
    {
        return $this->UPLOADED_VIDEO;
    }

    public function setUPLOADEDVIDEO(int $UPLOADED_VIDEO): static
    {
        $this->UPLOADED_VIDEO = $UPLOADED_VIDEO;

        return $this;
    }

    public function iSADMIN(): ?bool
    {
        return $this->IS_ADMIN;
    }

    public function setISADMIN(?bool $IS_ADMIN): static
    {
        $this->IS_ADMIN = $IS_ADMIN;

        return $this;
    }

    public function getAGE(): ?int
    {
        return $this->AGE;
    }

    public function setAGE(int $AGE): static
    {
        $this->AGE = $AGE;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->PASSWORD ?? '';
    }

    public function setPASSWORD(string $PASSWORD): static
    {
        $this->PASSWORD = $PASSWORD;
        return $this;
    }

    public function getEMAIL(): ?string
    {
        return $this->EMAIL;
    }

    public function setEMAIL(string $EMAIL): static
    {
        $this->EMAIL = $EMAIL;

        return $this;
    }

    public function getIPADRESSE(): ?string
    {
        return $this->IP_ADRESSE;
    }

    public function setIPADRESSE(?string $IP_ADRESSE): static
    {
        $this->IP_ADRESSE = $IP_ADRESSE;

        return $this;
    }

    public function getLASTLOGIN(): ?\DateTime
    {
        return $this->LAST_LOGIN;
    }

    public function setLASTLOGIN(?\DateTime $LAST_LOGIN): static
    {
        $this->LAST_LOGIN = $LAST_LOGIN;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles ?? ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return $this->EMAIL ;
    }
    public function eraseCredentials(): void
    {
    }
}

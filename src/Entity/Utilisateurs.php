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

    #[ORM\Column(nullable: true, name: "Certified")]
    private ?bool $certified = null;

    public function getCertified(): ?bool
    {
        return $this->certified;
    }

    public function setCertified(?bool $certified): void
    {
        $this->certified = $certified;
    }




    #[ORM\Column(length: 24, name: "PSEUDO")]
    #[Assert\Length(max: 24, maxMessage: "veuillez entrer un pseudo de moins de 24 lettres")]
    #[Assert\NotBlank(message: "veuillez entrer un pseudo")]
    private ?string $pseudo = null;

    #[ORM\Column(name: "SUBSCRIBERS")]
    private ?int $subscribers = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true, name: "JOIN_DATE")]
    private ?\DateTime $joinDate = null;

    #[ORM\Column(name: "UPLOADED_VIDEO")]
    private ?int $uploadedVideo = 0;

    #[ORM\Column(nullable: true, name: "IS_ADMIN")]
    private ?bool $isAdmin = null;

    #[ORM\Column(name: "AGE")]
    #[Assert\NotBlank]
    #[Assert\Range(min: 13, max: 120, notInRangeMessage: "veuillez entrer un age entre 13 et 120 ans")]
    private ?int $age = null;

    #[ORM\Column(length: 255, name: "PASSWORD")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 64)]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).+$/",
        message: "Le mot de passe doit contenir au moins une lettre un chiffre et un symbole."
    )]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true, name: "EMAIL")]
    #[Assert\Email]
    #[Assert\Length(max : 50)]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true, name: "IP_ADRESSE")]
    #[Assert\Ip]
    private ?string $ipAdresse = null;

    #[ORM\Column(nullable: true, name: "LAST_LOGIN")]
    private ?\DateTime $lastLogin = null;

    #[ORM\Column(nullable:true, name: "pfppath")]
    private ?string $pfppath = null;

    // Explicit column name to match the existing DB column (case-sensitive in some setups)
    #[ORM\Column(length: 255, nullable: true, name: "bannerpath")]
    private ?string $bannerpath = null;

    #[ORM\Column(length: 64, unique: true, nullable: true, name: "live_stream_key")]
    private ?string $liveStreamKey = null;

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
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getSubscribers(): ?int
    {
        return $this->subscribers;
    }

    public function setSubscribers(int $subscribers): static
    {
        $this->subscribers = $subscribers;

        return $this;
    }

    public function getJoinDate(): ?\DateTime
    {
        return $this->joinDate;
    }

    public function setJoinDate(?\DateTime $joinDate): static
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    public function getUploadedVideo(): ?int
    {
        return $this->uploadedVideo;
    }

    public function setUploadedVideo(int $uploadedVideo): static
    {
        $this->uploadedVideo = $uploadedVideo;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(?bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getIpAdresse(): ?string
    {
        return $this->ipAdresse;
    }

    public function setIpAdresse(?string $ipAdresse): static
    {
        $this->ipAdresse = $ipAdresse;

        return $this;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTime $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles ?? ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return $this->email ;
    }
    public function eraseCredentials(): void
    {
    }

    public function getBannerpath(): ?string
    {
        return $this->bannerpath;
    }

    public function setBannerpath(?string $bannerpath): static
    {
        $this->bannerpath = $bannerpath;

        return $this;
    }

    public function getLiveStreamKey(): ?string
    {
        return $this->liveStreamKey;
    }

    public function setLiveStreamKey(?string $liveStreamKey): static
    {
        $this->liveStreamKey = $liveStreamKey;

        return $this;
    }
}

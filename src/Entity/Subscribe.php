<?php

namespace App\Entity;

use App\Repository\SubscribeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscribeRepository::class)]
class Subscribe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $Subscribed_to = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $Subscribers = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscribedTo(): ?Utilisateurs
    {
        return $this->Subscribed_to;
    }

    public function setSubscribedTo(?Utilisateurs $Subscribed_to): static
    {
        $this->Subscribed_to = $Subscribed_to;

        return $this;
    }

    public function getSubscribers(): ?Utilisateurs
    {
        return $this->Subscribers;
    }

    public function setSubscribers(?Utilisateurs $Subscribers): static
    {
        $this->Subscribers = $Subscribers;

        return $this;
    }


}

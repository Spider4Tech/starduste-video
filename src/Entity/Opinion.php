<?php

namespace App\Entity;

use App\Repository\OpinionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpinionRepository::class)]
class Opinion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $user_id = null;

    #[ORM\ManyToOne]
    private ?Video $video_id = null;

    #[ORM\Column(length: 25)]
    private ?String $value = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?Utilisateurs
    {
        return $this->user_id;
    }

    public function setUserId(?Utilisateurs $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getVideoId(): ?Video
    {
        return $this->video_id;
    }

    public function setVideoId(?Video $video_id): static
    {
        $this->video_id = $video_id;

        return $this;
    }

    public function getValue(): ?String
    {
        return $this->value;
    }

    public function setValue(String $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}

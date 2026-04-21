<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?int $comlike = null;

    #[ORM\Column]
    private ?int $comdislike = null;

    #[ORM\Column(nullable: true)]
    private ?bool $favorited = null;

    #[ORM\ManyToOne(inversedBy: 'Comments')]
    #[ORM\JoinColumn(name: "video_id", nullable: false)]
    private ?Video $ComVideo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getComlike(): ?int
    {
        return $this->comlike;
    }

    public function setComlike(int $comlike): static
    {
        $this->comlike = $comlike;

        return $this;
    }

    public function getComdislike(): ?int
    {
        return $this->comdislike;
    }

    public function setComdislike(int $comdislike): static
    {
        $this->comdislike = $comdislike;

        return $this;
    }


    public function isFavorited(): ?bool
    {
        return $this->favorited;
    }

    public function setFavorited(?bool $favorited): static
    {
        $this->favorited = $favorited;

        return $this;
    }

    public function getComVideo(): ?Video
    {
        return $this->ComVideo;
    }

    public function setComVideo(?Video $ComVideo): static
    {
        $this->ComVideo = $ComVideo;

        return $this;
    }
}

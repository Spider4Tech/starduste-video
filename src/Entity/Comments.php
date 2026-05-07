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
    private ?int $comlike = 0;

    #[ORM\Column]
    private ?int $comdislike = 0;

    #[ORM\Column(nullable: true)]
    private ?bool $favorited = null;

    #[ORM\ManyToOne(targetEntity: Video::class)]
    #[ORM\JoinColumn(name: "CommentVideoId",referencedColumnName: "id", nullable: false)]
    private ?Video $ComVideo = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "CommentUploaderId", referencedColumnName: "id" ,nullable: false)]
    private ?Utilisateurs $CommentUploader = null;

    #[ORM\Column]
    private ?\DateTime $Date_Comment = null;

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

    public function getCommentUploader(): ?Utilisateurs
    {
        return $this->CommentUploader;
    }

    public function setCommentUploader(?Utilisateurs $CommentUploader): static
    {
        $this->CommentUploader = $CommentUploader;

        return $this;
    }

    public function getDateComment(): ?\DateTime
    {
        return $this->Date_Comment;
    }

    public function setDateComment(\DateTime $Date_Comment): static
    {
        $this->Date_Comment = $Date_Comment;

        return $this;
    }
}

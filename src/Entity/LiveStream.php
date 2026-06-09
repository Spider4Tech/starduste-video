<?php

namespace App\Entity;

use App\Repository\LiveStreamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiveStreamRepository::class)]
#[ORM\Table(name: 'live_stream')]
class LiveStream
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 128)]
    private ?string $title = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnailPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $playbackUrl = null;

    #[ORM\Column]
    private bool $live = false;

    #[ORM\Column]
    private int $viewers = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: 'streamer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateurs $streamer = null;

    public function __construct()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath;
    }

    public function setThumbnailPath(?string $thumbnailPath): static
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    public function getPlaybackUrl(): ?string
    {
        return $this->playbackUrl;
    }

    public function setPlaybackUrl(?string $playbackUrl): static
    {
        $this->playbackUrl = $playbackUrl;

        return $this;
    }

    public function isLive(): bool
    {
        return $this->live;
    }

    public function setLive(bool $live): static
    {
        $this->live = $live;

        return $this;
    }

    public function getViewers(): int
    {
        return $this->viewers;
    }

    public function setViewers(int $viewers): static
    {
        $this->viewers = max(0, $viewers);

        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTime $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStreamer(): ?Utilisateurs
    {
        return $this->streamer;
    }

    public function setStreamer(?Utilisateurs $streamer): static
    {
        $this->streamer = $streamer;

        return $this;
    }
}

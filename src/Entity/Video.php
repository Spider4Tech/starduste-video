<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32, unique: true)]
    private ?string $uuid = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getIsShort(): ?bool
    {
        return $this->isShort;
    }

    public function setIsShort(?bool $isShort): void
    {
        $this->isShort = $isShort;
    }
    #[ORM\Column]
    private ?bool $isShort = null;


    #[ORM\Column]
    private ?int $like_vid = null;

    #[ORM\Column(length: 28)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $video_duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $upload_date = null;

    #[ORM\Column]
    private ?int $dislike_vid = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie = null;

    #[ORM\Column]
    private ?int $views = null;



    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "uploader_id", referencedColumnName : "id", nullable: false)]
    private  ?Utilisateurs $uploader = null;

    public function getUploader(): ?Utilisateurs
    {
        return $this->uploader;
    }

    public function setUploader(?Utilisateurs $uploader): self
    {
        $this->uploader = $uploader;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikeVid(): ?int
    {
        return $this->like_vid;
    }

    public function setLikeVid(int $like_vid): static
    {
        $this->like_vid = $like_vid;

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

    public function getVideoDuration(): ?int
    {
        return $this->video_duration;
    }

    public function setVideoDuration(int $video_duration): static
    {
        $this->video_duration = $video_duration;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->video_url;
    }

    public function setVideoUrl(?string $video_url): static
    {
        $this->video_url = $video_url;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getUploadDate(): ?\DateTime
    {
        return $this->upload_date;
    }

    public function setUploadDate(?\DateTime $upload_date): static
    {
        $this->upload_date = $upload_date;

        return $this;
    }

    public function getDislikeVid(): ?int
    {
        return $this->dislike_vid;
    }

    public function setDislikeVid(int $dislike_vid): static
    {
        $this->dislike_vid = $dislike_vid;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;

        return $this;
    }



    public function getdurationformatted(): string{
        if ($this->video_duration === null){
            return '0:00';
        }

        $hours = floor($this->video_duration / 3600);
        $minute = floor(($this->video_duration / 3600) / 60);
        $seconds = $this->video_duration % 60;

        if ($hours > 0){
            return sprintf('%d:%02d:%02d', $hours, $minute, $seconds);
        }

        return sprintf('%02d:%02d',  $minute, $seconds);


    }
}

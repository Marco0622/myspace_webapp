<?php

namespace App\Entity;

use App\Repository\PicturesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'pic_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'pic_path', length: 255)]
    private ?string $path = null;

    #[ORM\Column(name: 'pic_size', type: Types::BIGINT)]
    private ?string $size = null;

    #[ORM\Column(name: 'pic_name', length: 100)]
    private ?string $name = null;

    #[ORM\Column(name: 'pic_created_at')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'sessionPictures')]
    #[ORM\JoinColumn(name: 'pic_session_id', nullable: false)]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'userPictures')]
    #[ORM\JoinColumn(name: 'pic_add_by', nullable: false)]
    private ?User $add_by = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getSession(): ?session
    {
        return $this->session;
    }

    public function setSession(?session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getAddBy(): ?user
    {
        return $this->add_by;
    }

    public function setAddBy(?user $add_by): static
    {
        $this->add_by = $add_by;

        return $this;
    }
}

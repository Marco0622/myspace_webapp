<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'Pages')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'pag_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'pag_name', length: 50)]
    private ?string $name = null;

    #[ORM\Column(name: 'pag_content', type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'sessionPages')]
    #[ORM\JoinColumn(name: 'pag_session_id', referencedColumnName: 'ses_id', nullable: false, onDelete: 'CASCADE')]
    private ?session $session = null;

    #[ORM\Column(name: 'pag_created_at',)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'userCreatedPages')]
    #[ORM\JoinColumn(name: 'pag_created_by', referencedColumnName: 'usr_id', nullable: false)]
    private ?user $created_by = null;

    #[ORM\Column(name: 'pag_edited_at', nullable: true)]
    private ?\DateTimeImmutable $edited_at = null;

    #[ORM\ManyToOne(inversedBy: 'userEditedPages')]
    #[ORM\JoinColumn(name: 'pag_edited_by', referencedColumnName: 'usr_id', nullable: true)]
    private ?user $edited_by = null;

    #[ORM\Column(name: 'pag_is_locked')]
    private ?bool $is_locked = null;

    #[ORM\ManyToOne(inversedBy: 'userLockedPages')]
    #[ORM\JoinColumn(name: 'pag_locked_by', referencedColumnName: 'usr_id', nullable: true)]
    private ?user $locked_by = null;

    #[ORM\Column(name: 'pag_locked_at', nullable: true)]
    private ?\DateTimeImmutable $locked_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?user
    {
        return $this->created_by;
    }

    public function setCreatedBy(?user $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->edited_at;
    }

    public function setEditedAt(\DateTimeImmutable $edited_at): static
    {
        $this->edited_at = $edited_at;

        return $this;
    }

    public function getEditedBy(): ?user
    {
        return $this->edited_by;
    }

    public function setEditedBy(?user $edited_by): static
    {
        $this->edited_by = $edited_by;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->is_locked;
    }

    public function setIsLocked(bool $is_locked): static
    {
        $this->is_locked = $is_locked;

        return $this;
    }

    public function getLockedBy(): ?user
    {
        return $this->locked_by;
    }

    public function setLockedBy(?user $locked_by): static
    {
        $this->locked_by = $locked_by;

        return $this;
    }

    public function getLockedAt(): ?\DateTimeImmutable
    {
        return $this->locked_at;
    }

    public function setLockedAt(?\DateTimeImmutable $locked_at): static
    {
        $this->locked_at = $locked_at;

        return $this;
    }
}

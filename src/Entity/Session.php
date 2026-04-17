<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'Sessions')]
class Session
{
   #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ses_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'ses_name', length: 60)]
    private ?string $name = null;

    #[ORM\Column(name: 'ses_is_blocked')]
    private ?bool $is_blocked = null;

    #[ORM\Column(name: 'ses_created_at')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(name: 'ses_last_activity', nullable: true)]
    private ?\DateTimeImmutable $last_activity = null;

    #[ORM\ManyToOne(inversedBy: 'storageSessions')]
    #[ORM\JoinColumn(name: 'ses_storage_id', referencedColumnName: 'sto_id', nullable: false)]
    private ?Storage $storage = null;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\OneToMany(targetEntity: Invitation::class, mappedBy: 'session', cascade: ['remove'])]
    private Collection $sessionInvitations;

    /**
     * @var Collection<int, Access>
     */
    #[ORM\OneToMany(targetEntity: Access::class, mappedBy: 'session', cascade: ['remove'])]
    private Collection $sessionAccesses;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'session', cascade: ['remove'])]
    private Collection $sessionPictures;

    /**
     * @var Collection<int, Page>
     */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'session', cascade: ['remove'])]
    private Collection $sessionPages;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\OneToMany(targetEntity: Node::class, mappedBy: 'session', cascade: ['remove'])]
    private Collection $sessionNodes;

    public function __construct()
    {
        $this->sessionInvitations = new ArrayCollection();
        $this->sessionAccesses = new ArrayCollection();
        $this->sessionPictures = new ArrayCollection();
        $this->sessionPages = new ArrayCollection();
        $this->sessionNodes = new ArrayCollection();
    }

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

    public function getIsBlocked(): ?bool
    {
        return $this->is_blocked;
    }

    public function setIsBlocked(bool $is_blocked): static
    {
        $this->is_blocked = $is_blocked;

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

    public function getLastActivity(): ?\DateTimeImmutable
    {
        return $this->last_activity;
    }

    public function setLastActivity(?\DateTimeImmutable $last_activity): static
    {
        $this->last_activity = $last_activity;

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSessionInvitations(): Collection
    {
        return $this->sessionInvitations;
    }

    public function addSessionInvitation(Invitation $sessionInvitation): static
    {
        if (!$this->sessionInvitations->contains($sessionInvitation)) {
            $this->sessionInvitations->add($sessionInvitation);
            $sessionInvitation->setSession($this);
        }

        return $this;
    }

    public function removeSessionInvitation(Invitation $sessionInvitation): static
    {
        if ($this->sessionInvitations->removeElement($sessionInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sessionInvitation->getSession() === $this) {
                $sessionInvitation->setSession(null);
            }
        }

        return $this;
    }

    public function getStorage(): ?storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return Collection<int, Access>
     */
    public function getSessionAccesses(): Collection
    {
        return $this->sessionAccesses;
    }

    public function addSessionAccess(Access $sessionAccess): static
    {
        if (!$this->sessionAccesses->contains($sessionAccess)) {
            $this->sessionAccesses->add($sessionAccess);
            $sessionAccess->setSession($this);
        }

        return $this;
    }

    public function removeSessionAccess(Access $sessionAccess): static
    {
        if ($this->sessionAccesses->removeElement($sessionAccess)) {
            // set the owning side to null (unless already changed)
            if ($sessionAccess->getSession() === $this) {
                $sessionAccess->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getSessionPictures(): Collection
    {
        return $this->sessionPictures;
    }

    public function addSessionPicture(Picture $sessionPicture): static
    {
        if (!$this->sessionPictures->contains($sessionPicture)) {
            $this->sessionPictures->add($sessionPicture);
            $sessionPicture->setSession($this);
        }

        return $this;
    }

    public function removeSessionPicture(Picture $sessionPicture): static
    {
        if ($this->sessionPictures->removeElement($sessionPicture)) {
            // set the owning side to null (unless already changed)
            if ($sessionPicture->getSession() === $this) {
                $sessionPicture->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getSessionPages(): Collection
    {
        return $this->sessionPages;
    }

    public function addSessionPage(Page $sessionPage): static
    {
        if (!$this->sessionPages->contains($sessionPage)) {
            $this->sessionPages->add($sessionPage);
            $sessionPage->setSession($this);
        }

        return $this;
    }

    public function removeSessionPage(Page $sessionPage): static
    {
        if ($this->sessionPages->removeElement($sessionPage)) {
            // set the owning side to null (unless already changed)
            if ($sessionPage->getSession() === $this) {
                $sessionPage->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Node>
     */
    public function getSessionNodes(): Collection
    {
        return $this->sessionNodes;
    }

    public function addSessionNode(Node $sessionNode): static
    {
        if (!$this->sessionNodes->contains($sessionNode)) {
            $this->sessionNodes->add($sessionNode);
            $sessionNode->setSession($this);
        }

        return $this;
    }

    public function removeSessionNode(Node $sessionNode): static
    {
        if ($this->sessionNodes->removeElement($sessionNode)) {
            // set the owning side to null (unless already changed)
            if ($sessionNode->getSession() === $this) {
                $sessionNode->setSession(null);
            }
        }

        return $this;
    }
}

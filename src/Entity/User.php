<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'Users')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'usr_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'usr_email', length: 180)]
    private ?string $email = null;

    #[ORM\Column(name: 'usr_roles')]
    private array $roles = [];

    #[ORM\Column(name: 'usr_password')]
    private ?string $password = null;

    #[ORM\Column(name: 'usr_name', length: 60)]
    private ?string $name = null;

    #[ORM\Column(name: 'usr_firstname', length: 60)]
    private ?string $firstname = null;

    #[ORM\Column(name: 'usr_pseudo', length: 60, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column(name: 'usr_photo', length: 100, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(name: 'usr_created_at')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(name: 'usr_updated_at', nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(name: 'usr_deleted_at', nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    #[ORM\Column(name: 'usr_ban_at', nullable: true)]
    private ?\DateTimeImmutable $ban_at = null;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\ManyToMany(targetEntity: Invitation::class, mappedBy: 'sender_id')]
    private Collection $invitations;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\OneToMany(targetEntity: Invitation::class, mappedBy: 'sender_id')]
    private Collection $sentInvitations;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\OneToMany(targetEntity: Invitation::class, mappedBy: 'receiver_id')]
    private Collection $receivedInvitations;

    /**
     * @var Collection<int, Access>
     */
    #[ORM\OneToMany(targetEntity: Access::class, mappedBy: 'member')]
    private Collection $userAccesses;

    /**
     * @var Collection<int, Pictures>
     */
    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'add_by')]
    private Collection $userPictures;

    /**
     * @var Collection<int, Page>
     */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'created_by')]
    private Collection $userCreatedPages;

    /**
     * @var Collection<int, Page>
     */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'edited_by')]
    private Collection $userEditedPages;

    /**
     * @var Collection<int, Page>
     */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'locked_by')]
    private Collection $userLockedPages;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'author')]
    private Collection $userSendReports;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\OneToMany(targetEntity: Node::class, mappedBy: 'add_by')]
    private Collection $userAddNodes;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->sentInvitations = new ArrayCollection();
        $this->receivedInvitations = new ArrayCollection();
        $this->userAccesses = new ArrayCollection();
        $this->userPictures = new ArrayCollection();
        $this->userCreatedPages = new ArrayCollection();
        $this->userEditedPages = new ArrayCollection();
        $this->userLockedPages = new ArrayCollection();
        $this->userSendReports = new ArrayCollection();
        $this->userAddNodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getBanAt(): ?\DateTimeImmutable
    {
        return $this->ban_at;
    }

    public function setBanAt(?\DateTimeImmutable $ban_at): static
    {
        $this->ban_at = $ban_at;

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setSenderId($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            $invitation->setSenderId(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSentInvitations(): Collection
    {
        return $this->sentInvitations;
    }

    public function addSentInvitation(Invitation $sentInvitation): static
    {
        if (!$this->sentInvitations->contains($sentInvitation)) {
            $this->sentInvitations->add($sentInvitation);
            $sentInvitation->setSenderId($this);
        }

        return $this;
    }

    public function removeSentInvitation(Invitation $sentInvitation): static
    {
        if ($this->sentInvitations->removeElement($sentInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sentInvitation->getSenderId() === $this) {
                $sentInvitation->setSenderId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }

    public function addReceivedInvitation(Invitation $receivedInvitation): static
    {
        if (!$this->receivedInvitations->contains($receivedInvitation)) {
            $this->receivedInvitations->add($receivedInvitation);
            $receivedInvitation->setReceiverId($this);
        }

        return $this;
    }

    public function removeReceivedInvitation(Invitation $receivedInvitation): static
    {
        if ($this->receivedInvitations->removeElement($receivedInvitation)) {
            // set the owning side to null (unless already changed)
            if ($receivedInvitation->getReceiverId() === $this) {
                $receivedInvitation->setReceiverId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Access>
     */
    public function getUserAccesses(): Collection
    {
        return $this->userAccesses;
    }

    public function addUserAccess(Access $userAccess): static
    {
        if (!$this->userAccesses->contains($userAccess)) {
            $this->userAccesses->add($userAccess);
            $userAccess->setMember($this);
        }

        return $this;
    }

    public function removeUserAccess(Access $userAccess): static
    {
        if ($this->userAccesses->removeElement($userAccess)) {
            // set the owning side to null (unless already changed)
            if ($userAccess->getMember() === $this) {
                $userAccess->setMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pictures>
     */
    public function getUserPictures(): Collection
    {
        return $this->userPictures;
    }

    public function addUserPicture(Pictures $userPicture): static
    {
        if (!$this->userPictures->contains($userPicture)) {
            $this->userPictures->add($userPicture);
            $userPicture->setAddBy($this);
        }

        return $this;
    }

    public function removeUserPicture(Pictures $userPicture): static
    {
        if ($this->userPictures->removeElement($userPicture)) {
            // set the owning side to null (unless already changed)
            if ($userPicture->getAddBy() === $this) {
                $userPicture->setAddBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getUserCreatedPages(): Collection
    {
        return $this->userCreatedPages;
    }

    public function addUserCreatedPage(Page $userCreatedPage): static
    {
        if (!$this->userCreatedPages->contains($userCreatedPage)) {
            $this->userCreatedPages->add($userCreatedPage);
            $userCreatedPage->setCreatedBy($this);
        }

        return $this;
    }

    public function removeUserCreatedPage(Page $userCreatedPage): static
    {
        if ($this->userCreatedPages->removeElement($userCreatedPage)) {
            // set the owning side to null (unless already changed)
            if ($userCreatedPage->getCreatedBy() === $this) {
                $userCreatedPage->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getUserEditedPages(): Collection
    {
        return $this->userEditedPages;
    }

    public function addUserEditedPage(Page $userEditedPage): static
    {
        if (!$this->userEditedPages->contains($userEditedPage)) {
            $this->userEditedPages->add($userEditedPage);
            $userEditedPage->setEditedBy($this);
        }

        return $this;
    }

    public function removeUserEditedPage(Page $userEditedPage): static
    {
        if ($this->userEditedPages->removeElement($userEditedPage)) {
            // set the owning side to null (unless already changed)
            if ($userEditedPage->getEditedBy() === $this) {
                $userEditedPage->setEditedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getUserLockedPages(): Collection
    {
        return $this->userLockedPages;
    }

    public function addUserLockedPage(Page $userLockedPage): static
    {
        if (!$this->userLockedPages->contains($userLockedPage)) {
            $this->userLockedPages->add($userLockedPage);
            $userLockedPage->setLockedBy($this);
        }

        return $this;
    }

    public function removeUserLockedPage(Page $userLockedPage): static
    {
        if ($this->userLockedPages->removeElement($userLockedPage)) {
            // set the owning side to null (unless already changed)
            if ($userLockedPage->getLockedBy() === $this) {
                $userLockedPage->setLockedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getUserSendReports(): Collection
    {
        return $this->userSendReports;
    }

    public function addUserSendReport(Report $userSendReport): static
    {
        if (!$this->userSendReports->contains($userSendReport)) {
            $this->userSendReports->add($userSendReport);
            $userSendReport->setAuthor($this);
        }

        return $this;
    }

    public function removeUserSendReport(Report $userSendReport): static
    {
        if ($this->userSendReports->removeElement($userSendReport)) {
            // set the owning side to null (unless already changed)
            if ($userSendReport->getAuthor() === $this) {
                $userSendReport->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Node>
     */
    public function getUserAddNodes(): Collection
    {
        return $this->userAddNodes;
    }

    public function addUserAddNode(Node $userAddNode): static
    {
        if (!$this->userAddNodes->contains($userAddNode)) {
            $this->userAddNodes->add($userAddNode);
            $userAddNode->setAddBy($this);
        }

        return $this;
    }

    public function removeUserAddNode(Node $userAddNode): static
    {
        if ($this->userAddNodes->removeElement($userAddNode)) {
            // set the owning side to null (unless already changed)
            if ($userAddNode->getAddBy() === $this) {
                $userAddNode->setAddBy(null);
            }
        }

        return $this;
    }
}

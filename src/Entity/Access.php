<?php

namespace App\Entity;

use App\Repository\AccessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessRepository::class)]
#[ORM\Table(name: 'Accesses')]
class Access
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'acc_id')] 
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userAccesses')]
    #[ORM\JoinColumn(name: 'acc_member_id', nullable: false, referencedColumnName: 'usr_id')] 
    private ?User $member = null;

    #[ORM\ManyToOne(inversedBy: 'sessionAccesses')]
    #[ORM\JoinColumn(name: 'acc_session_id', referencedColumnName: 'ses_id', nullable: false, onDelete: 'CASCADE')] 
    private ?Session $session = null;

    #[ORM\Column(name: 'acc_role', length: 20)] 
    private ?string $role = null;

    #[ORM\Column(name: 'acc_joined_at')] 
    private ?\DateTimeImmutable $joined_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): ?user
    {
        return $this->member;
    }

    public function setMember(?user $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joined_at;
    }

    public function setJoinedAt(\DateTimeImmutable $joined_at): static
    {
        $this->joined_at = $joined_at;

        return $this;
    }
}

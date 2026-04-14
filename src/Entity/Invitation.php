<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
#[ORM\Table(name: 'Invitations')]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'inv_id')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sentInvitations')]
    #[ORM\JoinColumn(name: "inv_sender_id", referencedColumnName: 'usr_id', nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receivedInvitations')]
    #[ORM\JoinColumn(name: "inv_receiver_id", referencedColumnName: 'usr_id', nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(name: 'inv_responce', nullable: true)]
    private ?bool $responce = null;

    #[ORM\Column(name: 'inv_send_at')]
    private ?\DateTimeImmutable $send_at = null;

    #[ORM\ManyToOne(inversedBy: 'sessionInvitations')]
    #[ORM\JoinColumn(name: 'inv_session_id', referencedColumnName: 'ses_id', nullable: false)]
    private ?Session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function isResponce(): ?bool
    {
        return $this->responce;
    }

    public function setResponce(?bool $responce): static
    {
        $this->responce = $responce;

        return $this;
    }

    public function getSendAt(): ?\DateTimeImmutable
    {
        return $this->send_at;
    }

    public function setSendAt(\DateTimeImmutable $send_at): static
    {
        $this->send_at = $send_at;

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
}

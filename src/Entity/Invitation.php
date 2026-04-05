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
    #[ORM\JoinColumn(name: "inv_sender_id", nullable: false)]
    private ?user $sender_id = null;

    #[ORM\ManyToOne(inversedBy: 'receivedInvitations')]
    #[ORM\JoinColumn(name: "inv_receiver_id", nullable: false)]
    private ?user $receiver_id = null;

    #[ORM\Column(name: 'inv_responce')]
    private ?bool $responce = null;

    #[ORM\Column(name: 'inv_send_at')]
    private ?\DateTimeImmutable $send_at = null;

    #[ORM\ManyToOne(inversedBy: 'sessionInvitations')]
    #[ORM\JoinColumn(name: 'inv_session_id', nullable: false)]
    private ?session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderId(): ?user
    {
        return $this->sender_id;
    }

    public function setSenderId(?user $sender_id): static
    {
        $this->sender_id = $sender_id;

        return $this;
    }

    public function getReceiverId(): ?user
    {
        return $this->receiver_id;
    }

    public function setReceiverId(?user $receiver_id): static
    {
        $this->receiver_id = $receiver_id;

        return $this;
    }

    public function isResponce(): ?bool
    {
        return $this->responce;
    }

    public function setResponce(bool $responce): static
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

    public function getSession(): ?session
    {
        return $this->session;
    }

    public function setSession(?session $session): static
    {
        $this->session = $session;

        return $this;
    }
}

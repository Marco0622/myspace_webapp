<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\Table(name: 'Reports')]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rep_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'rep_subject', length: 255)]
    private ?string $subject = null;

    #[ORM\Column(name: 'rep_content', type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(name: 'rep_send_at')]
    private ?\DateTimeImmutable $send_at = null;

    #[ORM\ManyToOne(inversedBy: 'userSendReports')]
    #[ORM\JoinColumn(name: 'rep_author_id', referencedColumnName: 'usr_id', nullable: false)]
    private ?user $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    public function getAuthor(): ?user
    {
        return $this->author;
    }

    public function setAuthor(?user $author): static
    {
        $this->author = $author;

        return $this;
    }
}

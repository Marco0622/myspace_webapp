<?php

namespace App\Entity;

use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NodeRepository::class)]
#[ORM\Table(name: 'Nodes')]
class Node
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'nod_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'nod_path', length: 255)]
    private ?string $path = null;

    #[ORM\Column(name: 'nod_size', type: Types::BIGINT)]
    private ?string $size = null;

    #[ORM\Column(name: 'nod_name', length: 80)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'sessionNodes')]
    #[ORM\JoinColumn(name: 'nod_session_id', referencedColumnName: 'ses_id', nullable: false)]
    private ?session $session = null;

    #[ORM\Column(name: 'nod_type', length: 10)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'userAddNodes')]
    #[ORM\JoinColumn(name: 'nod_add_by', referencedColumnName: 'usr_id', nullable: false)]
    private ?user $add_by = null;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[ORM\JoinColumn(name: 'nod_parent_id', referencedColumnName: 'nod_id', nullable: true, onDelete: 'CASCADE')]
    private Collection $parentNodes;

    public function __construct()
    {
        $this->parentNodes = new ArrayCollection();
    }

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

    public function getSession(): ?session
    {
        return $this->session;
    }

    public function setSession(?session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    /**
     * @return Collection<int, Node>
     */
    public function getParentNodes(): Collection
    {
        return $this->parentNodes;
    }

    public function addParentNode(self $parentNode): static
    {
        if (!$this->parentNodes->contains($parentNode)) {
            $this->parentNodes->add($parentNode);
            $parentNode->setParent($this);
        }

        return $this;
    }

    public function removeParentNode(self $parentNode): static
    {
        if ($this->parentNodes->removeElement($parentNode)) {
            // set the owning side to null (unless already changed)
            if ($parentNode->getParent() === $this) {
                $parentNode->setParent(null);
            }
        }

        return $this;
    }
}

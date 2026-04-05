<?php

namespace App\Entity;

use App\Repository\StorageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ORM\Table(name: 'Storages')]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'sto_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'sto_name', type: Types::BIGINT)]
    private ?string $size = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'storage')]
    private Collection $storageSessions;

    public function __construct()
    {
        $this->storageSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Session>
     */
    public function getStorageSessions(): Collection
    {
        return $this->storageSessions;
    }

    public function addStorageSession(Session $storageSession): static
    {
        if (!$this->storageSessions->contains($storageSession)) {
            $this->storageSessions->add($storageSession);
            $storageSession->setStorage($this);
        }

        return $this;
    }

    public function removeStorageSession(Session $storageSession): static
    {
        if ($this->storageSessions->removeElement($storageSession)) {
            // set the owning side to null (unless already changed)
            if ($storageSession->getStorage() === $this) {
                $storageSession->setStorage(null);
            }
        }

        return $this;
    }
}

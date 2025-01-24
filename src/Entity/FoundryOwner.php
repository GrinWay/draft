<?php

namespace App\Entity;

use App\Repository\FoundryOwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoundryOwnerRepository::class)]
class FoundryOwner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Foundry>
     */
    #[ORM\OneToMany(targetEntity: Foundry::class, mappedBy: 'owner')]
    private Collection $foundries;

    public function __construct()
    {
        $this->foundries = new ArrayCollection();
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

    /**
     * @return Collection<int, Foundry>
     */
    public function getFoundries(): Collection
    {
        return $this->foundries;
    }

    public function addFoundry(Foundry $foundry): static
    {
        if (!$this->foundries->contains($foundry)) {
            $this->foundries->add($foundry);
            $foundry->setOwner($this);
        }

        return $this;
    }

    public function removeFoundry(Foundry $foundry): static
    {
        if ($this->foundries->removeElement($foundry)) {
            // set the owning side to null (unless already changed)
            if ($foundry->getOwner() === $this) {
                $foundry->setOwner(null);
            }
        }

        return $this;
    }
}

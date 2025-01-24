<?php

namespace App\Entity;

use App\Repository\FoundryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoundryRepository::class)]
class Foundry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'foundries')]
    private ?FoundryOwner $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?FoundryOwner
    {
        return $this->owner;
    }

    public function setOwner(?FoundryOwner $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}

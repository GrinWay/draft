<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PizzaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PizzaRepository::class)]
#[ApiResource]
class Pizza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

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
}

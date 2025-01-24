<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\TrackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Post(),
    ],
)]
#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $latitude = null;

    #[ORM\Column(length: 20)]
    private ?string $longitude = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }
}

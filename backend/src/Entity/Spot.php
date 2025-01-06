<?php

namespace App\Entity;

use App\Repository\SpotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpotRepository::class)]
class Spot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private float $latitude;

    #[ORM\Column]
    private float $longitude;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}

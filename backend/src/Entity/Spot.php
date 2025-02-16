<?php

namespace App\Entity;

use App\Repository\SpotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isFavorite = null;

    #[ORM\ManyToOne(inversedBy: 'spots')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    /**
     * @var Collection<int, SpotPicture>
     */
    #[ORM\OneToMany(targetEntity: SpotPicture::class, mappedBy: 'spot', orphanRemoval: true)]
    private Collection $spotPictures;

    public function __construct()
    {
        $this->spotPictures = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function isFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): static
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, SpotPicture>
     */
    public function getSpotPictures(): Collection
    {
        return $this->spotPictures;
    }

    public function addSpotPicture(SpotPicture $spotPicture): static
    {
        if (!$this->spotPictures->contains($spotPicture)) {
            $this->spotPictures->add($spotPicture);
            $spotPicture->setSpot($this);
        }

        return $this;
    }

    public function removeSpotPicture(SpotPicture $spotPicture): static
    {
        if ($this->spotPictures->removeElement($spotPicture)) {
            // set the owning side to null (unless already changed)
            if ($spotPicture->getSpot() === $this) {
                $spotPicture->setSpot(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $emailVerified = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    private ?string $token = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Spot>
     */
    #[ORM\OneToMany(targetEntity: Spot::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $spots;

    /**
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'requester', orphanRemoval: true)]
    private Collection $friendshipsRequested;

    /**
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'receiver', orphanRemoval: true)]
    private Collection $friendshipsReceived;

    public function __construct()
    {
        $this->spots = new ArrayCollection();
        $this->friendshipsRequested = new ArrayCollection();
        $this->friendshipsReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): static
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, Spot>
     */
    public function getSpots(): Collection
    {
        return $this->spots;
    }

    public function addSpot(Spot $spot): static
    {
        if (!$this->spots->contains($spot)) {
            $this->spots->add($spot);
            $spot->setOwner($this);
        }

        return $this;
    }

    public function removeSpot(Spot $spot): static
    {
        if ($this->spots->removeElement($spot)) {
            // set the owning side to null (unless already changed)
            if ($spot->getOwner() === $this) {
                $spot->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendshipsRequested(): Collection
    {
        return $this->friendshipsRequested;
    }

    public function addFriendshipsRequested(Friendship $friendshipsRequested): static
    {
        if (!$this->friendshipsRequested->contains($friendshipsRequested)) {
            $this->friendshipsRequested->add($friendshipsRequested);
            $friendshipsRequested->setRequester($this);
        }

        return $this;
    }

    public function removeFriendshipsRequested(Friendship $friendshipsRequested): static
    {
        if ($this->friendshipsRequested->removeElement($friendshipsRequested)) {
            // set the owning side to null (unless already changed)
            if ($friendshipsRequested->getRequester() === $this) {
                $friendshipsRequested->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendshipsReceived(): Collection
    {
        return $this->friendshipsReceived;
    }

    public function addFriendshipsReceived(Friendship $friendshipsReceived): static
    {
        if (!$this->friendshipsReceived->contains($friendshipsReceived)) {
            $this->friendshipsReceived->add($friendshipsReceived);
            $friendshipsReceived->setReceiver($this);
        }

        return $this;
    }

    public function removeFriendshipsReceived(Friendship $friendshipsReceived): static
    {
        if ($this->friendshipsReceived->removeElement($friendshipsReceived)) {
            // set the owning side to null (unless already changed)
            if ($friendshipsReceived->getReceiver() === $this) {
                $friendshipsReceived->setReceiver(null);
            }
        }

        return $this;
    }
}

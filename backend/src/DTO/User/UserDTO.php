<?php

namespace App\DTO\User;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $id,

        #[Groups(['read', 'create'])]
        #[Assert\NotBlank(groups: ['read', 'create'])]
        #[Assert\Email(groups: ['read', 'create'])]
        public readonly string $email,

        #[Groups(['read'])]
        #[Assert\Type('bool', groups: ['read'])]
        public readonly ?bool $emailVerified,

        #[Groups(['create'])]
        #[Assert\NotBlank(groups: ['create'])]
        #[Assert\Length(min: 8, groups: ['create'])]
        public readonly string $password,

        #[Groups(['read', 'create'])]
        #[Assert\NotBlank(groups: ['read', 'create'])]
        public readonly string $pseudo,

        #[Assert\NotNull(groups: ['read'])]
        #[Assert\DateTime(groups: ['read'])]
        public readonly ?\DateTimeImmutable $createdAt,

        #[Assert\NotNull(groups: ['read'])]
        #[Assert\DateTime(groups: ['read'])]
        public readonly ?\DateTime $updatedAt,

        #[Assert\NotBlank(allowNull: true, groups: ['read', 'create'])]
        public readonly ?string $picture,

        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $token,
    ) {
    }
}

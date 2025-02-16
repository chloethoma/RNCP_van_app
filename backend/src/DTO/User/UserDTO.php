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

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update'])]
        #[Assert\Email(groups: ['read', 'create', 'update'])]
        public readonly string $email,

        #[Groups(['read', 'update'])]
        #[Assert\Type('bool', groups: ['read', 'update'])]
        public readonly ?bool $emailVerified,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update'])]
        public readonly string $pseudo,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\DateTime(groups: ['read'])]
        public readonly ?\DateTimeImmutable $createdAt,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\DateTime(groups: ['read'])]
        public readonly ?\DateTime $updatedAt,

        #[Groups(['read', 'update'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read', 'update'])]
        public readonly ?string $picture,

        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $token,

        #[Groups(['create', 'update'])]
        #[Assert\NotBlank(allowNull: true, groups: ['create', 'update'])]
        #[Assert\Length(min: 8, groups: ['create', 'update'])]
        public readonly ?string $password = null,
    ) {
    }
}

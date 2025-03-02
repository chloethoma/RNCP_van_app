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

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update'])]
        #[Assert\Length(min: 3, max: 50, groups: ['read', 'create', 'update'])]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_]+$/', message: 'Only letters, numbers, and underscores are allowed', groups: ['read', 'create', 'update'])]
        public readonly string $pseudo,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Type(\DateTimeInterface::class, groups: ['read'])]
        #[Assert\LessThanOrEqual('now', groups: ['read'])]
        public readonly ?\DateTimeInterface $createdAt,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Type(\DateTime::class, groups: ['read'])]
        #[Assert\GreaterThanOrEqual(propertyPath: 'createdAt', groups: ['read'])]
        public readonly ?\DateTimeInterface $updatedAt,

        #[Groups(['read', 'update'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read', 'update'])]
        public readonly ?string $picture,

        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $token,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\Type('bool', groups: ['read', 'create', 'update'])]
        public readonly ?bool $emailVerified = false,

        #[Groups(['create'])]
        #[Assert\NotBlank(allowNull: true, groups: ['create'])]
        #[Assert\Length(min: 8, groups: ['create'])]
        public readonly ?string $password = null,
    ) {
    }
}

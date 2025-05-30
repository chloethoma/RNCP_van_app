<?php

namespace App\DTO\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'User',
    description: 'User identity',
)]
class UserDTO
{
    public function __construct(
        #[OA\Property(
            description: 'User id',
            example: 12
        )]
        #[Groups(['read', 'search_read'])]
        #[Assert\NotNull(groups: ['read', 'search_read'])]
        public readonly ?int $id,

        #[OA\Property(
            description: 'Email of the user',
            example: 'jane.doe@gmail.com'
        )]
        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update'])]
        #[Assert\Email(groups: ['read', 'create', 'update'])]
        public readonly string $email,

        #[OA\Property(
            description: 'Pseudo of the user',
            example: 'jane_doe'
        )]
        #[Groups(['read', 'create', 'update', 'search_read'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update', 'search_read'])]
        #[Assert\Length(min: 3, max: 50, groups: ['read', 'create', 'update', 'search_read'])]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_]+$/', message: 'Only letters, numbers, and underscores are allowed', groups: ['read', 'create', 'update', 'search_read'])]
        public readonly string $pseudo,

        #[OA\Property(
            description: 'Creation date of the account'
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Type(\DateTimeInterface::class, groups: ['read'])]
        #[Assert\LessThanOrEqual('now', groups: ['read'])]
        public readonly ?\DateTimeInterface $createdAt,

        #[OA\Property(
            description: 'Updated date of the account'
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Type(\DateTime::class, groups: ['read'])]
        #[Assert\GreaterThanOrEqual(propertyPath: 'createdAt', groups: ['read'])]
        public readonly ?\DateTimeInterface $updatedAt,

        #[OA\Property(
            description: 'Choosen avatar for profil picture'
        )]
        #[Groups(['read', 'update', 'search_read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read', 'update', 'search_read'])]
        public readonly ?string $picture,

        #[OA\Property(
            description: 'JWT token for a session'
        )]
        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $token,

        #[OA\Property(
            description: 'Not implemented yet'
        )]
        #[Groups(['read', 'create', 'update'])]
        #[Assert\Type('bool', groups: ['read', 'create', 'update'])]
        public readonly ?bool $emailVerified = false,

        #[OA\Property(
            description: 'Hashed password'
        )]
        #[Groups(['create'])]
        #[Assert\NotBlank(allowNull: true, groups: ['create'])]
        #[Assert\Length(min: 8, groups: ['create'])]
        public readonly ?string $password = null,
    ) {
    }
}

<?php

namespace App\DTO\User;

use App\Services\Validator as CustomAssert;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'User password',
    description: 'Update password DTO',
)]
class UserPasswordDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Current password',
        )]
        #[Groups(['update'])]
        #[Assert\NotBlank(groups: ['update'])]
        public readonly string $currentPassword,

        #[OA\Property(
            description: 'New password',
        )]
        #[Groups(['update'])]
        #[Assert\NotBlank(groups: ['update'])]
        #[Assert\PasswordStrength(groups: ['update'])]
        #[Assert\NotCompromisedPassword(groups: ['update'])]
        #[CustomAssert\NotIdenticalPassword(groups: ['update'])]
        public readonly string $newPassword,
    ) {
    }
}

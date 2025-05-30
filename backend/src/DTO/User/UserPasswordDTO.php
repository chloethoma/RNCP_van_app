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
            description: 'Plain current password',
        )]
        #[Groups(['update'])]
        #[Assert\NotBlank(groups: ['update'])]
        #[Assert\Length(min: 8, groups: ['update'])]
        public readonly string $currentPassword,

        #[OA\Property(
            description: 'Plain new password',
        )]
        #[Groups(['update'])]
        #[Assert\NotBlank(groups: ['update'])]
        #[Assert\Length(min: 8, groups: ['update'])]
        #[CustomAssert\NotIdenticalPassword(groups: ['update'])]
        public readonly string $newPassword,
    ) {
    }
}

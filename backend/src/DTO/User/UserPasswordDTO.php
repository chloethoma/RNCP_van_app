<?php

namespace App\DTO\User;

use App\Services\Validator as CustomAssert;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordDTO
{
    public function __construct(
        #[Groups(['update'])]
        #[Assert\NotBlank(groups: ['update'])]
        #[Assert\Length(min: 8, groups: ['update'])]
        public readonly string $currentPassword,

        #[Groups(['update'])]
        #[Assert\NotBlank(groups: ['update'])]
        #[Assert\Length(min: 8, groups: ['update'])]
        #[CustomAssert\NotIdenticalPassword(groups: ['update'])]
        public readonly string $newPassword,
    ) {
    }
}

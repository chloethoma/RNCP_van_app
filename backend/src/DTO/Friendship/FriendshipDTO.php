<?php

namespace App\DTO\Friendship;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class FriendshipDTO
{
    public function __construct(
        #[Groups(['read', 'create'])]
        #[Assert\NotNull(groups: ['read', 'create'])]
        public readonly int $receiverId,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $requesterId,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $id,

        #[Groups(['read', 'create'])]
        #[Assert\NotNull(groups: ['read', 'create'])]
        public readonly bool $isConfirmed = false,
    ) {
    }
}

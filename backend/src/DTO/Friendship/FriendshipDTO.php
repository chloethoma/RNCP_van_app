<?php

namespace App\DTO\Friendship;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class FriendshipDTO
{
    public function __construct(
        #[Groups(['read', 'update'])]
        #[Assert\NotNull(groups: ['read', 'update'])]
        #[Assert\Valid(groups: ['read', 'update'])]
        public readonly ?FriendshipUserDTO $requester,

        #[Groups(['read', 'create'])]
        #[Assert\NotNull(groups: ['read', 'create'])]
        #[Assert\Valid(groups: ['read', 'create'])]
        public readonly FriendshipUserDTO $receiver,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        #[Assert\Type(type: 'boolean', groups: ['read', 'create', 'update'])]
        public readonly bool $isConfirmed = false,
    ) {
    }
}

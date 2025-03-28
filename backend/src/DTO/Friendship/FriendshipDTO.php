<?php

namespace App\DTO\Friendship;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class FriendshipDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Valid(groups: ['read'])]
        public readonly ?FriendshipUserDTO $requester,

        #[Groups(['read', 'create'])]
        #[Assert\NotNull(groups: ['read', 'create'])]
        #[Assert\Valid(groups: ['read', 'create'])]
        public readonly FriendshipUserDTO $receiver,

        #[Groups(['read', 'create'])]
        #[Assert\NotNull(groups: ['read', 'create'])]
        #[Assert\Type(type: 'boolean', groups: ['read', 'create'])]
        public readonly bool $isConfirmed = false,
    ) {
    }
}

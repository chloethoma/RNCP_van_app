<?php

namespace App\DTO\Friendship;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class FriendshipReceivedSummaryDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly int $count,
    ) {
    }
}

<?php

namespace App\DTO\User;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserSummaryDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly int $friendsNumber,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly int $spotsNumber,
    ) {
    }
}

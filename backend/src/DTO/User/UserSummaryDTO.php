<?php

namespace App\DTO\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'User summary',
    description: 'Extra infos about user',
)]
class UserSummaryDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Number of friends',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly int $friendsNumber,

        #[OA\Property(
            description: 'Number of spots',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly int $spotsNumber,
    ) {
    }
}

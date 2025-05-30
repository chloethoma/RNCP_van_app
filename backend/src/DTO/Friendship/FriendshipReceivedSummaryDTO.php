<?php

namespace App\DTO\Friendship;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'Friendship received summary',
    description: 'Number of received friendship request',
)]
class FriendshipReceivedSummaryDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Number of received friendship request',
            example: 3
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly int $count,
    ) {
    }
}

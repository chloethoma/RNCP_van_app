<?php

namespace App\DTO\Friendship;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'Partial Friendship',
    description: 'Details of a friendship without details of requester/receiver',
)]
class PartialFriendshipDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Friend identity',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Valid(groups: ['read'])]
        public readonly FriendshipUserDTO $friend,

        #[OA\Property(
            description: 'Friendship is confirmed or not',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Type(type: 'boolean', groups: ['read'])]
        public readonly bool $isConfirmed,
    ) {
    }
}

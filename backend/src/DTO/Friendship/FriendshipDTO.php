<?php

namespace App\DTO\Friendship;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'Friendship',
    description: 'Details of a friendship',
)]
class FriendshipDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Requester identity',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Valid(groups: ['read'])]
        public readonly ?FriendshipUserDTO $requester,

        #[OA\Property(
            description: 'Receiver identity',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Valid(groups: ['read'])]
        public readonly FriendshipUserDTO $receiver,

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

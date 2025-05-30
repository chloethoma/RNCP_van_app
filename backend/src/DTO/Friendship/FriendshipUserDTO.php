<?php

namespace App\DTO\Friendship;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'Friendship user identity',
    description: 'Identity of a friend',
)]
class FriendshipUserDTO
{
    public function __construct(
        #[OA\Property(
            description: 'User id',
            example: 2
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $id,

        #[OA\Property(
            description: 'User pseudo',
            example: 'Jane Doe'
        )]
        #[Groups(['read'])]
        #[Assert\NotBlank(groups: ['read'])]
        public readonly ?string $pseudo,

        #[OA\Property(
            description: 'Choosen avatar for profil picture'
        )]
        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $picture,
    ) {
    }
}

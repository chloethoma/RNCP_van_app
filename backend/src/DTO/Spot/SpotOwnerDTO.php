<?php

namespace App\DTO\Spot;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'Spot Owner',
    description: 'Spot owner identity',
)]
class SpotOwnerDTO
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

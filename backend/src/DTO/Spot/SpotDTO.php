<?php

namespace App\DTO\Spot;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'Spot',
    description: 'Details of a spot',
)]
class SpotDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Latitude',
            example: 48.8896087
        )]
        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        #[Assert\Type(type: 'float', groups: ['read', 'create', 'update'])]
        #[Assert\Range(min: -90, max: 90, groups: ['read', 'create', 'update'])]
        public readonly float $latitude,

        #[OA\Property(
            description: 'Longitude',
            example: 2.2003227
        )]
        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        #[Assert\Type(type: 'float', groups: ['read', 'create', 'update'])]
        #[Assert\Range(min: -180, max: 180, groups: ['read', 'create', 'update'])]
        public readonly float $longitude,

        #[OA\Property(
            description: 'Spot description',
            example: 'Un super spot en bord de mer'
        )]
        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update'])]
        public readonly ?string $description,

        #[OA\Property(
            description: 'Spot owner identity'
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        #[Assert\Valid(groups: ['read'])]
        public readonly ?SpotOwnerDTO $owner,

        #[OA\Property(
            description: 'Spot is in favorite list or not'
        )]
        #[Groups(['read', 'create', 'update'])]
        #[Assert\Type(type: 'boolean', groups: ['read', 'create', 'update'])]
        public readonly bool $isFavorite = false,

        #[OA\Property(
            description: 'Spot id',
            example: 12
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $id = null,
    ) {
    }
}

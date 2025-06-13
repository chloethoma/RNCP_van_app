<?php

namespace App\DTO\SpotGeoJson;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'GeoJSON properties',
    description: 'Informations about a spot',
)]
class SpotPropertiesDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Spot id',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public int $spotId,

        #[OA\Property(
            description: 'Owner id of a spot',
        )]
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public int $ownerId,
    ) {
    }
}

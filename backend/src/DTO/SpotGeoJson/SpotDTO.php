<?php

namespace App\DTO\SpotGeoJson;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'GeoJSON spot',
    description: 'GeoJSON schema for a spot',
)]
class SpotDTO
{
    public const TYPE = 'Feature';

    public function __construct(
        #[OA\Property(
            description: 'GeoJSON geometry for a spot',
        )]
        #[Groups(['read'])]
        #[Assert\Valid(groups: ['read'])]
        public SpotGeometryDTO $geometry,

        #[OA\Property(
            description: 'GeoJSON properties for a spot',
        )]
        #[Groups(['read'])]
        #[Assert\Valid(groups: ['read'])]
        public SpotPropertiesDTO $properties,

        #[OA\Property(
            description: 'GeoJSON type (const Feature)',
        )]
        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,
    ) {
    }
}

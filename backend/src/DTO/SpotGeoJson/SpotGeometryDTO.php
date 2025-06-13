<?php

namespace App\DTO\SpotGeoJson;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'GeoJSON geometry',
    description: 'GeoJSON geometry for a spot',
)]
class SpotGeometryDTO
{
    public const TYPE = 'Point';

    public function __construct(
        #[OA\Property(
            description: 'Coordinates [longitude, latitude]',
            example: [2.2003227, 48.8896087]
        )]
        #[Groups(['read'])]
        #[Assert\Type(type: 'array', groups: ['read'])]
        #[Assert\Count(min: 2, max: 2, groups: ['read'])]
        #[Assert\All([
            new Assert\Type(type: 'float', groups: ['read']),
        ])]
        public array $coordinates,

        #[OA\Property(
            description: 'The type of the geometry',
        )]
        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,
    ) {
    }
}

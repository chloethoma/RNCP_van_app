<?php

namespace App\DTO\SpotGeoJson;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'GeoJSON spot collection',
    description: 'GeoJSON schema for a list of spot',
)]
class SpotCollectionDTO
{
    public const TYPE = 'FeatureCollection';

    public function __construct(
        #[OA\Property(
            description: 'GeoJSON type (const FeatureCollection)',
        )]
        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,

        #[OA\Property(
            description: 'List of GeoJSON spots'
        )]
        #[Assert\Valid(groups: ['read'])]
        #[Assert\All([
            new Assert\Type(type: SpotDTO::class, groups: ['read']),
        ])]
        /**
         * @var SpotDTO[]
         */
        public array $features = [],
    ) {
    }
}

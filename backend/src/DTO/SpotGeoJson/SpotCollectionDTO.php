<?php

namespace App\DTO\SpotGeoJson;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotCollectionDTO
{
    public const TYPE = 'FeatureCollection';

    public function __construct(
        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,

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

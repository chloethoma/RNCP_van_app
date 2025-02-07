<?php

namespace App\DTO\Feature;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotFeatureCollectionDTO
{
    public const TYPE = 'FeatureCollection';

    public function __construct(
        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,

        #[Assert\Valid(groups: ['read'])]
        #[Assert\All([
            new Assert\Type(type: SpotFeatureDTO::class, groups: ['read']),
        ])]
        /**
         * @var SpotFeatureDTO[]
         */
        public array $features = [],
    ) {
    }
}

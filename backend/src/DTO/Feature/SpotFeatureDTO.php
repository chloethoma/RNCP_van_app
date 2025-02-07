<?php

namespace App\DTO\Feature;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotFeatureDTO
{
    public const TYPE = 'Feature';

    public function __construct(
        #[Groups(['read'])]
        #[Assert\Valid(groups: ['read'])]
        public SpotGeometryDTO $geometry,

        #[Groups(['read'])]
        #[Assert\Valid(groups: ['read'])]
        public SpotPropertiesDTO $properties,

        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,
    ) {
    }
}

<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotFeatureDTO
{
    public const TYPE = 'Feature';

    #[Assert\IdenticalTo(self::TYPE)]
    public string $type = self::TYPE;

    #[Assert\Valid]
    #[Assert\Type(SpotGeometryDTO::class)]
    public SpotGeometryDTO $geometry;

    #[Assert\Valid]
    #[Assert\Type(SpotPropertiesDTO::class)]
    public SpotPropertiesDTO $properties;

    public function __construct(
        SpotGeometryDTO $geometry,
        SpotPropertiesDTO $properties,
    ) {
        $this->geometry = $geometry;
        $this->properties = $properties;
    }
}

<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotFeatureOutput
{
    public const TYPE = 'Feature';

    #[Assert\IdenticalTo(self::TYPE)]
    public string $type = self::TYPE;

    #[Assert\Valid]
    #[Assert\Type(SpotGeometryOutput::class)]
    public SpotGeometryOutput $geometry;

    #[Assert\Valid]
    #[Assert\Type(SpotPropertiesOutput::class)]
    public SpotPropertiesOutput $properties;

    public function __construct(
        SpotGeometryOutput $geometry,
        SpotPropertiesOutput $properties,
    ) {
        $this->geometry = $geometry;
        $this->properties = $properties;
    }
}

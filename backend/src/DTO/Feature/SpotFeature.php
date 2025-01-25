<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotFeature
{
    public const TYPE = 'Feature';

    #[Assert\IdenticalTo(self::TYPE)]
    public string $type = self::TYPE;

    #[Assert\Valid]
    #[Assert\Type(SpotGeometry::class)]
    public SpotGeometry $geometry;

    #[Assert\Valid]
    #[Assert\Type(SpotProperties::class)]
    public SpotProperties $properties;

    public function __construct(
        SpotGeometry $geometry,
        SpotProperties $properties,
    ) {
        $this->geometry = $geometry;
        $this->properties = $properties;
    }
}

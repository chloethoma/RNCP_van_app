<?php

namespace App\DTO\Feature;

use App\DTO\Feature\SpotGeometryOutput;
use App\DTO\Feature\SpotPropertiesOutput;

class SpotFeatureOutput
{
    public string $type = 'Feature';

    public SpotGeometryOutput $geometry;

    public SpotPropertiesOutput $properties;


    public function __construct(
        SpotGeometryOutput $geometry,
        SpotPropertiesOutput $properties
    ) {
        $this->geometry = $geometry;
        $this->properties = $properties;
    }
}
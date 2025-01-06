<?php 

namespace App\DTO\Feature;

class SpotGeometryOutput
{
    public string $type = 'Point';

    public array $coordinates;

    public function __construct(
        float $longitude,
        float $latitude
    )
    {
        $this->coordinates = [$longitude, $latitude];
    }
}
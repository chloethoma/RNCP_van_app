<?php 

namespace App\DTO\Feature;

use App\DTO\SpotFeatureOutput;

class SpotFeatureCollectionOutput
{
    public string $type = 'FeatureCollection';

    /**
     * @var SpotFeatureOutput[]
     */
    public array $features = [];

    public function __construct(array $features)
    {
        $this->features = $features;
    }
}
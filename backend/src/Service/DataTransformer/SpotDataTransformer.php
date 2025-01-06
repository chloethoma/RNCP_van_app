<?php

namespace App\Service\DataTransformer;

use App\DTO\Feature\SpotFeatureCollectionOutput;
use App\DTO\Feature\SpotFeatureOutput;
use App\DTO\Feature\SpotGeometryOutput;
use App\DTO\Feature\SpotPropertiesOutput;

class SpotDataTransformer
{
    public function transformToFeatureCollection(array $spotsEntities): SpotFeatureCollectionOutput
    {
        $features = [];

        foreach($spotsEntities as $spotEntity) {
            $geometry = new SpotGeometryOutput(
                $spotEntity->getLongitude(),
                $spotEntity->getLatitude()
            );

            $properties = new SpotPropertiesOutput($spotEntity->getId());

            $features[] = new SpotFeatureOutput($geometry, $properties);
        };

        return new SpotFeatureCollectionOutput($features);
    }
}

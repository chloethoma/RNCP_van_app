<?php

namespace App\Service\DataTransformer;

use App\DTO\Feature\SpotFeature;
use App\DTO\Feature\SpotFeatureCollection;
use App\DTO\Feature\SpotGeometry;
use App\DTO\Feature\SpotProperties;
use App\Service\Validator\Validator;

class SpotDataTransformer
{
    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function transformToFeatureCollection(array $spotsEntities): SpotFeatureCollection
    {
        $features = [];

        foreach ($spotsEntities as $spotEntity) {
            $geometry = new SpotGeometry(
                $spotEntity->getLongitude(),
                $spotEntity->getLatitude()
            );

            $properties = new SpotProperties($spotEntity->getId());

            $features[] = new SpotFeature($geometry, $properties);
        }

        $spotFeatureCollection = new SpotFeatureCollection($features);

        $this->validator->validate($spotFeatureCollection, SpotFeatureCollection::class);

        return $spotFeatureCollection;
    }
}

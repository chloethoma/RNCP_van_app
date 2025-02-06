<?php

namespace App\Service\DataTransformer;

use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Feature\SpotFeatureDTO;
use App\DTO\Feature\SpotGeometryDTO;
use App\DTO\Feature\SpotPropertiesDTO;
use App\Service\Validator\Validator;
use Symfony\Component\Serializer\SerializerInterface;

class FeatureDataTransformer
{
    public function __construct(
        protected Validator $validator,
        protected SerializerInterface $serializer,
    ) {
    }

    public function mapEntityToDTO(array $entities): SpotFeatureCollectionDTO
    {
        $features = [];
        foreach ($entities as $spot) {
            $geometry = new SpotGeometryDTO(
                $spot->getLongitude(),
                $spot->getLatitude()
            );

            $properties = new SpotPropertiesDTO($spot->getId());

            $features[] = new SpotFeatureDTO($geometry, $properties);
        }

        $spotFeatureCollection = new SpotFeatureCollectionDTO($features);

        $this->validator->validate($spotFeatureCollection, SpotFeatureCollectionDTO::class);

        return $spotFeatureCollection;
    }
}

<?php

namespace App\DataTransformer;

use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Feature\SpotFeatureDTO;
use App\DTO\Feature\SpotGeometryDTO;
use App\DTO\Feature\SpotPropertiesDTO;
use App\Entity\SpotCollection;
use App\Service\Validator\Validator;

class FeatureDataTransformer
{
    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function mapEntityListToDTOList(SpotCollection $spotCollection): SpotFeatureCollectionDTO
    {
        $featureList = [];

        foreach ($spotCollection as $spot) {
            $geometry = new SpotGeometryDTO(
                coordinates: [$spot->getLongitude(), $spot->getLatitude()]
            );

            $properties = new SpotPropertiesDTO(
                id: $spot->getId()
            );

            $feature = new SpotFeatureDTO(
                geometry: $geometry,
                properties: $properties
            );

            $featureList[] = $feature;
        }

        $spotFeatureCollection = new SpotFeatureCollectionDTO(
            features: $featureList);

        $this->validator->validate($spotFeatureCollection, SpotFeatureCollectionDTO::class);

        return $spotFeatureCollection;
    }

    public function transformArrayInObjectList(array $spotList): SpotCollection
    {
        $spotCollection = new SpotCollection();
        foreach ($spotList as $spot) {
            $spotCollection->append($spot);
        }

        return $spotCollection;
    }
}

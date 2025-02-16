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
    private SpotCollection $entityList;

    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function setEntityList(SpotCollection $entityList): void
    {
        $this->entityList = $entityList;
    }

    public function mapEntityListToDTOList(): SpotFeatureCollectionDTO
    {
        $featureList = [];

        foreach ($this->entityList as $entity) {
            $geometry = new SpotGeometryDTO(
                coordinates: [$entity->getLongitude(), $entity->getLatitude()]
            );

            $properties = new SpotPropertiesDTO(
                id: $entity->getId()
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

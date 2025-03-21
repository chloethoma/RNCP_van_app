<?php

namespace App\DataTransformer;

use App\DTO\SpotGeoJson\SpotCollectionDTO;
use App\DTO\SpotGeoJson\SpotDTO;
use App\DTO\SpotGeoJson\SpotGeometryDTO;
use App\DTO\SpotGeoJson\SpotPropertiesDTO;
use App\Entity\SpotCollection;
use App\Services\Validator\Validator;

class SpotListDataTransformer
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

    public function mapEntityListToDTOList(): SpotCollectionDTO
    {
        $spotList = [];

        foreach ($this->entityList as $entity) {
            $geometry = new SpotGeometryDTO(
                coordinates: [$entity->getLongitude(), $entity->getLatitude()]
            );

            $properties = new SpotPropertiesDTO(
                id: $entity->getId()
            );

            $spot = new SpotDTO(
                geometry: $geometry,
                properties: $properties
            );

            $spotList[] = $spot;
        }

        $spotCollection = new SpotCollectionDTO(
            features: $spotList);

        $this->validator->validate($spotCollection, SpotCollectionDTO::class);

        return $spotCollection;
    }

    public function transformArrayToObjectList(array $spotList): SpotCollection
    {
        $spotCollection = new SpotCollection();
        foreach ($spotList as $spot) {
            $spotCollection->append($spot);
        }

        return $spotCollection;
    }
}

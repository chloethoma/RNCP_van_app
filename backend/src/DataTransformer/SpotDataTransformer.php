<?php

namespace App\DataTransformer;

use App\DTO\Spot\SpotDTO;
use App\Entity\Spot;
use App\Service\Validator\Validator;

class SpotDataTransformer
{
    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function mapDTOtoEntity(SpotDTO $dto): Spot
    {
        $spot = new Spot();

        $spot->setLatitude($dto->latitude);
        $spot->setLongitude($dto->longitude);
        $spot->setDescription($dto->description);
        $spot->setIsFavorite($dto->isFavorite);

        return $spot;
    }

    public function mapEntityToDTO(Spot $entity): SpotDTO
    {
        return new SpotDTO(
            id: $entity->getId(),
            latitude: $entity->getLatitude(),
            longitude: $entity->getLongitude(),
            description: $entity->getDescription(),
            isFavorite: $entity->isFavorite(),
            userId: $entity->getOwner()->getId()
        );
    }
}

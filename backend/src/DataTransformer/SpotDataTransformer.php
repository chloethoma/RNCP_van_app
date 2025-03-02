<?php

namespace App\DataTransformer;

use App\DTO\Spot\SpotDTO;
use App\Entity\Spot;
use App\Services\Validator\Validator;

class SpotDataTransformer
{
    private ?Spot $entity = null;
    private SpotDTO $dto;

    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function setEntity(Spot $entity): void
    {
        $this->entity = $entity;
    }

    public function setDTO(SpotDTO $dto): void
    {
        $this->dto = $dto;
    }

    public function mapDTOtoEntity(): Spot
    {
        $spot = new Spot();

        if (null !== $this->entity) {
            $spot = $this->entity;
        }

        $spot->setLatitude($this->dto->latitude);
        $spot->setLongitude($this->dto->longitude);
        $spot->setDescription($this->dto->description);
        $spot->setIsFavorite($this->dto->isFavorite);

        return $spot;
    }

    public function mapEntityToDTO(): SpotDTO
    {
        $dto = new SpotDTO(
            id: $this->entity->getId(),
            latitude: $this->entity->getLatitude(),
            longitude: $this->entity->getLongitude(),
            description: $this->entity->getDescription(),
            isFavorite: $this->entity->isFavorite(),
            userId: $this->entity->getOwner()->getId()
        );

        $this->validator->validate($dto, SpotDTO::class, ['read']);

        return $dto;
    }
}

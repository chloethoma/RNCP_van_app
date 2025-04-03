<?php

namespace App\DataTransformer;

use App\DTO\Spot\SpotDTO;
use App\DTO\Spot\SpotOwnerDTO;
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
        $owner = new SpotOwnerDTO(
            id: $this->entity->getOwner()->getId(),
            pseudo: $this->entity->getOwner()->getPseudo(),
            picture: $this->entity->getOwner()->getPicture()
        );

        $dto = new SpotDTO(
            id: $this->entity->getId(),
            latitude: $this->entity->getLatitude(),
            longitude: $this->entity->getLongitude(),
            description: $this->entity->getDescription(),
            isFavorite: $this->entity->isFavorite(),
            owner: $owner
        );

        $this->validator->validate($dto, SpotDTO::class, ['read']);

        return $dto;
    }
}

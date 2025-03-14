<?php

namespace App\DataTransformer;

use App\DTO\Friendship\FriendshipDTO;
use App\Entity\Friendship;
use App\Services\Validator\Validator;

class FriendshipDataTransformer
{
    private ?Friendship $entity = null;
    private FriendshipDTO $dto;

    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function setEntity(Friendship $entity): void
    {
        $this->entity = $entity;
    }

    public function setDTO($dto): void
    {
        $this->dto = $dto;
    }

    public function mapDTOToEntity(): Friendship
    {
        $friendship = new Friendship();

        if (null !== $this->entity) {
            $friendship = $this->entity;
        }

        $friendship->setConfirmed($this->dto->isConfirmed);

        return $friendship;
    }

    public function mapEntityToDTO(): FriendshipDTO
    {
        $dto = new FriendshipDTO(
            receiverId: $this->entity->getReceiver()->getId(),
            requesterId: $this->entity->getRequester()->getId(),
            isConfirmed: $this->entity->isConfirmed(),
            id: $this->entity->getId(),
        );

        $this->validator->validate($dto, FriendshipDTO::class, ['read']);

        return $dto;
    }
}

<?php

namespace App\DataTransformer;

use App\DTO\Friendship\FriendshipDTO;
use App\DTO\Friendship\FriendshipUserDTO;
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
        $requester = new FriendshipUserDTO(
            id: $this->entity->getRequester()->getId(),
            pseudo: $this->entity->getRequester()->getPseudo(),
            picture: $this->entity->getRequester()->getPicture()
        );

        $receiver = new FriendshipUserDTO(
            id: $this->entity->getReceiver()->getId(),
            pseudo: $this->entity->getReceiver()->getPseudo(),
            picture: $this->entity->getReceiver()->getPicture()
        );

        $dto = new FriendshipDTO(
            requester: $requester,
            receiver: $receiver,
            isConfirmed: $this->entity->isConfirmed(),
        );

        $this->validator->validate($dto, FriendshipDTO::class, ['read']);

        return $dto;
    }
}

<?php

namespace App\DataTransformer;

use App\DTO\Friendship\FriendshipDTO;
use App\DTO\Friendship\FriendshipUserDTO;
use App\Entity\Friendship;
use App\Entity\User;
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
            requester: $this->mapFriendshipUser($this->entity->getRequester()),
            receiver: $this->mapFriendshipUser($this->entity->getReceiver()),
            isConfirmed: $this->entity->isConfirmed(),
        );

        $this->validator->validate($dto, FriendshipDTO::class, ['read']);

        return $dto;
    }

    private function mapFriendshipUser(User $entity): FriendshipUserDTO
    {
        $dto = new FriendshipUserDTO(
            id: $entity->getId(),
            pseudo: $entity->getPseudo(),
            picture: $entity->getPicture()
        );

        return $dto;
    }
}

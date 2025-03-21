<?php

namespace App\DataTransformer;

use App\DTO\Friendship\FriendshipDTO;
use App\DTO\Friendship\FriendshipUserDTO;
use App\Entity\Friendship;
use App\Entity\FriendshipCollection;
use App\Entity\User;
use App\Services\Validator\Validator;

class FriendshipListDataTransformer
{
    private FriendshipCollection $entityList;

    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function setEntityList(FriendshipCollection $entityList): void
    {
        $this->entityList = $entityList;
    }

    public function mapEntityListToDTOList(): \ArrayObject
    {
        $friendshipList = new \ArrayObject();

        foreach ($this->entityList as $entity) {
            $friendshipList->append($this->mapFriendship($entity));
        }

        return $friendshipList;
    }

    public function transformArrayToObjectList(array $friendshipList): FriendshipCollection
    {
        $friendshipCollection = new FriendshipCollection();
        foreach ($friendshipList as $friendship) {
            $friendshipCollection->append($friendship);
        }

        return $friendshipCollection;
    }

    private function mapFriendship(Friendship $entity): FriendshipDTO
    {
        $dto = new FriendshipDTO(
            requester: $this->mapFriendshipUser($entity->getRequester()),
            receiver: $this->mapFriendshipUser($entity->getReceiver()),
            isConfirmed: $entity->isConfirmed()
        );

        $this->validator->validate($dto, FriendshipDTO::class);

        return $dto;
    }

    private function mapFriendshipUser(User $entity): FriendshipUserDTO
    {
        $dto = new FriendshipUserDTO(
            id: $entity->getId(),
            pseudo: $entity->getPseudo(),
            picture: $entity->getPicture()
        );

        $this->validator->validate($dto, FriendshipUserDTO::class);

        return $dto;
    }
}

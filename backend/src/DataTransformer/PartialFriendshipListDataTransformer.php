<?php

namespace App\DataTransformer;

use App\DTO\Friendship\FriendshipUserDTO;
use App\DTO\Friendship\PartialFriendshipDTO;
use App\Entity\Friendship;
use App\Entity\FriendshipCollection;
use App\Entity\User;
use App\Services\Validator\Validator;

class PartialFriendshipListDataTransformer
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

    public function mapEntityListToDTOList(int $userId): \ArrayObject
    {
        $friendshipList = new \ArrayObject();

        foreach ($this->entityList as $entity) {
            $friendshipList->append($this->mapFriend($entity, $userId));
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

    private function mapFriend(Friendship $entity, int $userId): PartialFriendshipDTO
    {
        if ($entity->getRequester()->getId() === $userId) {
            $dto = new PartialFriendshipDTO(
                friend: $this->mapFriendshipUser($entity->getReceiver()),
                isConfirmed: $entity->isConfirmed()
            );
        } else {
            $dto = new PartialFriendshipDTO(
                friend: $this->mapFriendshipUser($entity->getRequester()),
                isConfirmed: $entity->isConfirmed()
            );
        }

        $this->validator->validate($dto, PartialFriendshipDTO::class);

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

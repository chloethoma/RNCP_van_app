<?php

namespace App\DataTransformer;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Entity\UserCollection;
use App\Services\Validator\Validator;

class UserListDataTransformer
{
    private UserCollection $entityList;

    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function setEntityList(UserCollection $entityList): void
    {
        $this->entityList = $entityList;
    }

    public function mapEntityListToDTOList(): \ArrayObject
    {
        $userList = new \ArrayObject();

        foreach ($this->entityList as $entity) {
            $userList->append($this->mapUser($entity));
        }

        return $userList;
    }

    public function transformArrayToObjectList(array $userList): UserCollection
    {
        $userCollection = new UserCollection();
        foreach ($userList as $user) {
            $userCollection->append($user);
        }

        return $userCollection;
    }

    private function mapUser(User $entity): UserDTO
    {
        $dto = new UserDTO(
            id: $entity->getId(),
            email: $entity->getEmail(),
            emailVerified: $entity->isEmailVerified(),
            password: null,
            pseudo: $entity->getPseudo(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
            picture: $entity->getPicture(),
            token: $entity->getToken()
        );

        $this->validator->validate($dto, UserDTO::class);

        return $dto;
    }
}

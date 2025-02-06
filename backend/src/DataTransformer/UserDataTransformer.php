<?php

namespace App\DataTransformer;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Service\Validator\Validator;

class UserDataTransformer
{
    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function mapDTOToEntity(UserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPseudo($dto->pseudo);
        $user->setEmailVerified(false);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTime());

        return $user;
    }

    public function mapEntityToDTO(User $entity): UserDTO
    {
        return new UserDTO(
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
    }
}

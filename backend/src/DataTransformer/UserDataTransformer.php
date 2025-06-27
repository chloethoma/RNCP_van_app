<?php

namespace App\DataTransformer;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Services\Validator\Validator;

class UserDataTransformer
{
    private ?User $entity = null;
    private UserDTO $dto;

    public function __construct(
        protected Validator $validator,
    ) {
    }

    public function setEntity(User $entity): void
    {
        $this->entity = $entity;
    }

    public function setDTO($dto): void
    {
        $this->dto = $dto;
    }

    public function mapDTOToEntity(): User
    {
        $user = new User();

        if (null !== $this->entity) {
            // case update
            $user = $this->entity;
        } else {
            // case create
            $user->setCreatedAt(new \DateTimeImmutable());
        }

        $user->setEmail($this->dto->email);
        $user->setEmailVerified($this->dto->emailVerified);
        $user->setPseudo($this->dto->pseudo);
        $user->setPicture($this->dto->picture);
        $user->setUpdatedAt(new \DateTime());

        return $user;
    }

    public function mapEntityToDTO(): UserDTO
    {
        $dto = new UserDTO(
            id: $this->entity->getId(),
            email: $this->entity->getEmail(),
            emailVerified: $this->entity->isEmailVerified(),
            password: null,
            pseudo: $this->entity->getPseudo(),
            token: $this->entity->getToken(),
            createdAt: $this->entity->getCreatedAt(),
            updatedAt: $this->entity->getUpdatedAt(),
            picture: $this->entity->getPicture(),
        );

        $this->validator->validate($dto, UserDTO::class, ['read']);

        return $dto;
    }
}

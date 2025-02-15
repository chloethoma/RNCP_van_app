<?php

namespace App\DataTransformer;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Service\Validator\Validator;

class UserDataTransformer
{
    private User $entity;
    private UserDTO $dto;

    public function __construct(
        protected Validator $validator,
    ) {
    }
    
    public function setEntity(User $entity): void
    {
        $this->entity = $entity;
    }

    public function setDTO(UserDTO $dto): void
    {
        $this->dto = $dto;
    }

    public function mapDTOToEntity(): User
    {
        $user = new User();
        $user->setEmail($this->dto->email);
        $user->setPseudo($this->dto->pseudo);
        $user->setEmailVerified(false);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTime());

        return $user;
    }

    public function mapEntityToDTO(): UserDTO
    {
        return new UserDTO(
            id: $this->entity->getId(),
            email: $this->entity->getEmail(),
            emailVerified: $this->entity->isEmailVerified(),
            password: null,
            pseudo: $this->entity->getPseudo(),
            createdAt: $this->entity->getCreatedAt(),
            updatedAt: $this->entity->getUpdatedAt(),
            picture: $this->entity->getPicture(),
            token: $this->entity->getToken()
        );
    }
}

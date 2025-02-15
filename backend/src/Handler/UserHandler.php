<?php

namespace App\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\Repository\UserRepository;
use App\Service\Manager\UserManager;

class UserHandler
{
    public function __construct(
        protected UserDataTransformer $transformer,
        protected UserManager $manager,
        protected UserRepository $repository,
    ) {
    }

    public function handleCreate(UserDTO $dto): UserDTO
    {
        $this->transformer->setDTO($dto);
        $user = $this->transformer->mapDTOToEntity();

        $this->manager->checkIfUserAlreadyExists($user);

        $user = $this->manager->hashPassword($user, $dto->password);
        $user = $this->repository->createUser($user);

        $user = $this->manager->createToken($user);

        $this->transformer->setEntity($user);
        return $this->transformer->mapEntityToDTO();
    }
}

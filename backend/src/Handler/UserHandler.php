<?php

namespace App\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\Repository\UserRepository;
use App\Service\Manager\UserManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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

        if ($this->manager->isEmailAlreadyTaken($user)) {
            throw new ConflictHttpException('User already exists with this email');
        }

        if ($this->manager->isPseudoAlreadyTaken($user)) {
            throw new ConflictHttpException('User already exists with this pseudo');
        }

        $user = $this->manager->hashPassword($user, $dto->password);
        $user = $this->repository->create($user);

        $user = $this->manager->createToken($user);

        $this->transformer->setEntity($user);

        return $this->transformer->mapEntityToDTO();
    }

    public function handleGet(int $userId): UserDTO
    {
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$this->manager->checkAccess($user)) {
            throw new AccessDeniedHttpException();
        }

        $this->transformer->setEntity($user);

        return $this->transformer->mapEntityToDTO();
    }

    public function handleUpdate(int $userId, UserDTO $dto): UserDTO
    {
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$this->manager->checkAccess($user)) {
            throw new AccessDeniedHttpException();
        }

        $this->transformer->setEntity($user);
        $this->transformer->setDTO($dto);
        $user = $this->transformer->mapDTOToEntity();

        if ($this->manager->isEmailAlreadyTaken($user)) {
            throw new ConflictHttpException('User already exists with this email');
        }

        if ($this->manager->isPseudoAlreadyTaken($user)) {
            throw new ConflictHttpException('User already exists with this pseudo');
        }

        $newUser = $this->repository->update($user);

        $this->transformer->setEntity($newUser);

        return $this->transformer->mapEntityToDTO();
    }
}

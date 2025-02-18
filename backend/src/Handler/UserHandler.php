<?php

namespace App\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\Exceptions\Client\EmailConflictException;
use App\Exceptions\Client\PseudoConflictException;
use App\Manager\UserManager;
use App\Repository\UserRepository;
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

        // if ($this->manager->isEmailAlreadyTaken($user)) {
        //     throw new EmailConflictException();
        // }

        // if ($this->manager->isPseudoAlreadyTaken($user)) {
        //     throw new PseudoConflictException();
        // }
        $this->manager->checkEmailOrPseudoAlreadyTaken($user);

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

        // if ($this->manager->isEmailAlreadyTaken($user)) {
        //     throw new ConflictHttpException('User already exists with this email');
        // }

        // if ($this->manager->isPseudoAlreadyTaken($user)) {
        //     throw new ConflictHttpException('User already exists with this pseudo');
        // }
        $this->manager->checkEmailOrPseudoAlreadyTaken($user);

        $newUser = $this->repository->update($user);

        $this->transformer->setEntity($newUser);

        return $this->transformer->mapEntityToDTO();
    }
}

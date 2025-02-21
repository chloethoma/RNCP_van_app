<?php

namespace App\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserHandler
{
    public function __construct(
        protected UserDataTransformer $transformer,
        protected UserManager $manager,
        protected UserRepository $repository,
        protected EntityManagerInterface $em,
    ) {
    }

    public function handleCreate(UserDTO $dto): UserDTO
    {
        $this->transformer->setDTO($dto);
        $user = $this->transformer->mapDTOToEntity();

        $this->manager->checkEmailOrPseudoAlreadyTaken($user);

        $user = $this->manager->hashPassword($user, $dto->password);

        $this->em->persist($user);
        $this->em->flush();

        $user = $this->manager->createToken($user);

        $this->transformer->setEntity($user);

        return $this->transformer->mapEntityToDTO();
    }

    public function handleGet(int $userId): UserDTO
    {
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $this->manager->checkAccess($user);

        $this->transformer->setEntity($user);

        return $this->transformer->mapEntityToDTO();
    }

    public function handleUpdate(int $userId, UserDTO $dto): UserDTO
    {
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $this->manager->checkAccess($user);

        $this->transformer->setEntity($user);
        $this->transformer->setDTO($dto);
        $updatedUser = $this->transformer->mapDTOToEntity();

        $this->manager->checkEmailOrPseudoAlreadyTaken($user);

        $this->em->flush();

        $this->transformer->setEntity($updatedUser);

        return $this->transformer->mapEntityToDTO();
    }

    public function handleDelete(int $userId): void
    {
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $this->manager->checkAccess($user);

        $this->em->remove($user);
        $this->em->flush();
    }
}

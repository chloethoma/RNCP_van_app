<?php

namespace App\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\DTO\User\UserPasswordDTO;
use App\Entity\User;
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

    public function handleGet(): UserDTO
    {
        $this->transformer->setEntity($this->getUser());

        return $this->transformer->mapEntityToDTO();
    }

    public function handleUpdate(UserDTO $dto): UserDTO
    {
        $user = $this->getUser();

        $this->transformer->setEntity($user);
        $this->transformer->setDTO($dto);
        $updatedUser = $this->transformer->mapDTOToEntity();

        $this->manager->checkEmailOrPseudoAlreadyTaken($updatedUser);

        $this->em->flush();

        $this->transformer->setEntity($updatedUser);

        return $this->transformer->mapEntityToDTO();
    }

    public function handleUpdatePassword(UserPasswordDTO $dto): void
    {
        $user = $this->getUser();

        $this->manager->checkCurrentPasswordValidity($user, $dto->currentPassword);
        $user = $this->manager->hashPassword($user, $dto->newPassword);

        $this->em->flush();
    }

    public function handleDelete(): void
    {
        $this->em->remove($this->getUser());
        $this->em->flush();
    }

    private function getUser(): User
    {
        $userId = $this->manager->getOwner();
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}

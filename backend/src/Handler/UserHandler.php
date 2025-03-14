<?php

namespace App\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DataTransformer\UserListDataTransformer;
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
        protected UserDataTransformer $userTransformer,
        protected UserListDataTransformer $userListTransformer,
        protected UserManager $manager,
        protected UserRepository $repository,
        protected EntityManagerInterface $em,
    ) {
    }

    public function handleCreate(UserDTO $dto): UserDTO
    {
        $this->userTransformer->setDTO($dto);
        $user = $this->userTransformer->mapDTOToEntity();

        $this->manager->checkEmailOrPseudoAlreadyTaken($user);

        $user = $this->manager->hashPassword($user, $dto->password);

        $this->em->persist($user);
        $this->em->flush();

        $user = $this->manager->createToken($user);

        $this->userTransformer->setEntity($user);

        return $this->userTransformer->mapEntityToDTO();
    }

    public function handleGet(): UserDTO
    {
        $this->userTransformer->setEntity($this->getUser());

        return $this->userTransformer->mapEntityToDTO();
    }

    public function handleUpdate(UserDTO $dto): UserDTO
    {
        $user = $this->getUser();

        $this->userTransformer->setEntity($user);
        $this->userTransformer->setDTO($dto);
        $updatedUser = $this->userTransformer->mapDTOToEntity();

        $this->manager->checkEmailOrPseudoAlreadyTaken($updatedUser);

        $this->em->flush();

        $this->userTransformer->setEntity($updatedUser);

        return $this->userTransformer->mapEntityToDTO();
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

    /**
     * @return \ArrayObject<int, UserDTO>
     */
    public function handleSearchUser(string $pseudo): \ArrayObject
    {
        $user = $this->getUser();

        $userList = $this->repository->searchUsersNotFriendsWithCurrentUser($pseudo, $user->getId());

        $userCollection = $this->userListTransformer->transformArrayToObjectList($userList);
        $this->userListTransformer->setEntityList($userCollection);

        return $this->userListTransformer->mapEntityListToDTOList();
    }

    private function getUser(): User
    {
        $userId = $this->manager->getOwnerId();
        $user = $this->repository->findByUserIdentifier($userId);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}

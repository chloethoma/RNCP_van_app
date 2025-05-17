<?php

namespace App\Handler;

use App\DataTransformer\PartialFriendshipListDataTransformer;
use App\DataTransformer\SpotListDataTransformer;
use App\DataTransformer\UserDataTransformer;
use App\DataTransformer\UserListDataTransformer;
use App\DTO\User\UserDTO;
use App\DTO\User\UserPasswordDTO;
use App\DTO\User\UserSummaryDTO;
use App\Manager\UserManager;
use App\Repository\FriendshipRepository;
use App\Repository\SpotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserHandler
{
    public function __construct(
        protected UserDataTransformer $userTransformer,
        protected UserListDataTransformer $userListTransformer,
        protected UserManager $manager,
        protected UserRepository $userRepository,
        protected SpotRepository $spotRepository,
        protected FriendshipRepository $friendshipRepository,
        protected PartialFriendshipListDataTransformer $friendshipListTransformer,
        protected SpotListDataTransformer $spotListTransformer,
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
        $this->userTransformer->setEntity($this->manager->getAuthenticatedUser());

        return $this->userTransformer->mapEntityToDTO();
    }

    public function handleUpdate(UserDTO $dto): UserDTO
    {
        $user = $this->manager->getAuthenticatedUser();

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
        $user = $this->manager->getAuthenticatedUser();

        $this->manager->checkCurrentPasswordValidity($user, $dto->currentPassword);
        $user = $this->manager->hashPassword($user, $dto->newPassword);

        $this->em->flush();
    }

    public function handleDelete(): void
    {
        $this->em->remove($this->manager->getAuthenticatedUser());
        $this->em->flush();
    }

    /**
     * @return \ArrayObject<int, UserDTO>
     */
    public function handleSearchUser(string $pseudo): \ArrayObject
    {
        $user = $this->manager->getAuthenticatedUser();

        $userList = $this->userRepository->searchUsersNotFriendsWithCurrentUser($pseudo, $user->getId());

        $userCollection = $this->userListTransformer->transformArrayToObjectList($userList);
        $this->userListTransformer->setEntityList($userCollection);

        return $this->userListTransformer->mapEntityListToDTOList();
    }

    public function handleGetUserSummary(): UserSummaryDTO
    {
        $user = $this->manager->getAuthenticatedUser();

        $friendshipList = $this->friendshipRepository->findConfirmFriendships($user->getId());
        $spotList = $this->spotRepository->findCollection($user->getId());

        return new UserSummaryDTO(
            friendsNumber: count($friendshipList),
            spotsNumber: count($spotList)
        );
    }
}

<?php

namespace App\Manager;

use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Services\Exceptions\Friendship\FriendshipConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class FriendshipManager
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserManager $userManager,
        protected FriendshipRepository $repository,
    ) {
    }

    public function initNewFriendship(): Friendship
    {
        $friendship = new Friendship();
        $friendship->setConfirmed(false);

        return $friendship;
    }

    public function initAuthenticatedUser(Friendship $friendship): Friendship
    {
        $user = $this->findUserById($this->userManager->getAuthenticatedUserId());

        $friendship->setRequester($user);

        return $friendship;
    }

    public function initFriendUser(int $friendUserId, Friendship $friendship): Friendship
    {
        $friendUser = $this->findUserById($friendUserId);

        $friendship->setReceiver($friendUser);

        return $friendship;
    }

    public function isReceiverIdDifferentFromCurrentUser(int $userId, int $receiverId): bool
    {
        return $userId !== $receiverId;
    }

    /**
     * @throws FriendshipConflictException
     */
    public function checkIfFriendshipAlreadyExists(Friendship $friendship): void
    {
        if ($this->repository->isFriendshipExist($friendship->getRequester()->getId(), $friendship->getReceiver()->getId())) {
            throw new FriendshipConflictException();
        }
    }

    /**
     * @throws UserNotFoundException
     */
    private function findUserById(int $id): User
    {
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function initConfirmFriendship(Friendship $friendship): Friendship
    {
        $friendship->setConfirmed(true);

        return $friendship;
    }
}

<?php

namespace App\Manager;

use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendshipManager
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserManager $userManager,
        protected FriendshipRepository $repository,
    ) {
    }

    public function initOwner(Friendship $friendship): Friendship
    {
        $userId = $this->userManager->getOwnerId();

        $userRepository = $this->em->getRepository(User::class);
        $owner = $userRepository->find($userId);

        if (!$owner) {
            throw new NotFoundHttpException();
        }

        $friendship->setRequester($owner);

        return $friendship;
    }

    public function initFriendUser(int $friendUserId, Friendship $friendship): Friendship
    {
        $userRepository = $this->em->getRepository(User::class);
        $friendUser = $userRepository->find($friendUserId);

        if (!$friendUser) {
            throw new NotFoundHttpException();
        }

        $friendship->setReceiver($friendUser);

        return $friendship;
    }

    public function isReceiverIdDifferentFromCurrentUser(int $userId, int $receiverId): bool
    {
        return $userId !== $receiverId;
    }

    public function checkIfFriendshipAlreadyExists(Friendship $friendship): void
    {
        if ($this->repository->countFriendships($friendship->getRequester()->getId(), $friendship->getReceiver()->getId()) > 0) {
            throw new ConflictHttpException();
        }
    }
}

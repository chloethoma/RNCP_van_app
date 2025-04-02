<?php

namespace App\Handler;

use App\DataTransformer\FriendshipDataTransformer;
use App\DataTransformer\PartialFriendshipListDataTransformer;
use App\DTO\Friendship\FriendshipDTO;
use App\DTO\Friendship\FriendshipReceivedSummaryDTO;
use App\DTO\Friendship\PartialFriendshipDTO;
use App\Manager\FriendshipManager;
use App\Manager\UserManager;
use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendshipHandler
{
    public function __construct(
        protected UserManager $userManager,
        protected FriendshipManager $friendshipManager,
        protected FriendshipDataTransformer $friendshipTransformer,
        protected PartialFriendshipListDataTransformer $friendshipListTransformer,
        protected EntityManagerInterface $em,
        protected UserRepository $userRepository,
        protected FriendshipRepository $friendshipRepository,
    ) {
    }

    public function handleCreate(int $friendId): FriendshipDTO
    {
        $friendship = $this->friendshipManager->initNewFriendship();

        $friendship = $this->friendshipManager->initAuthenticatedUser($friendship);
        $friendship = $this->friendshipManager->initFriendUser($friendId, $friendship);

        if (!$this->friendshipManager->isReceiverIdDifferentFromCurrentUser($friendship->getRequester()->getId(), $friendship->getReceiver()->getId())) {
            throw new BadRequestHttpException();
        }

        $this->friendshipManager->checkIfFriendshipAlreadyExists($friendship);

        $this->em->persist($friendship);
        $this->em->flush();

        $this->friendshipTransformer->setEntity($friendship);

        return $this->friendshipTransformer->mapEntityToDTO();
    }

    /**
     * @return \ArrayObject<int, PartialFriendshipDTO>
     */
    public function handleGetPendingFriendships(string $type): \ArrayObject
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $pendingList = $this->friendshipRepository->findPendingFriendshipsByUserIdAndType($userId, $type);
        $pendingCollection = $this->friendshipListTransformer->transformArrayToObjectList($pendingList);

        $this->friendshipListTransformer->setEntityList($pendingCollection);

        return $this->friendshipListTransformer->mapEntityListToDTOList($userId);
    }

    public function handleGetReceivedFriendshipSummary(): FriendshipReceivedSummaryDTO
    {
        $receivedFriendshipList = $this->handleGetPendingFriendships('received');

        $summary = count($receivedFriendshipList);

        return new FriendshipReceivedSummaryDTO($summary);
    }

    /**
     * @return \ArrayObject<int, PartialFriendshipDTO>
     */
    public function handleGetConfirmFriendshipList(): \ArrayObject
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $friendshipList = $this->friendshipRepository->findConfirmFriendships($userId);
        $friendshipCollection = $this->friendshipListTransformer->transformArrayToObjectList($friendshipList);

        $this->friendshipListTransformer->setEntityList($friendshipCollection);

        return $this->friendshipListTransformer->mapEntityListToDTOList($userId);
    }

    public function handleConfirmFriendship(int $requesterId): FriendshipDTO
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $friendship = $this->friendshipRepository->findOneFriendshipById($requesterId, $userId);

        if (!$friendship) {
            throw new NotFoundHttpException();
        }

        $friendship = $this->friendshipManager->initConfirmFriendship($friendship);

        $this->em->flush();

        $this->friendshipTransformer->setEntity($friendship);

        return $this->friendshipTransformer->mapEntityToDTO();
    }

    public function handleDeleteFriendship(int $friendId): void
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $friendship = $this->friendshipRepository->findOneFriendshipById($friendId, $userId);

        if (!$friendship) {
            throw new NotFoundHttpException();
        }

        $this->em->remove($friendship);
        $this->em->flush();
    }
}

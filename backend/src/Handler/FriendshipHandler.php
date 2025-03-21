<?php

namespace App\Handler;

use App\DataTransformer\FriendshipDataTransformer;
use App\DataTransformer\FriendshipListDataTransformer;
use App\DTO\Friendship\FriendshipDTO;
use App\Manager\FriendshipManager;
use App\Manager\UserManager;
use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FriendshipHandler
{
    public function __construct(
        protected UserManager $userManager,
        protected FriendshipManager $friendshipManager,
        protected FriendshipDataTransformer $friendshipTransformer,
        protected FriendshipListDataTransformer $friendshipListTransformer,
        protected EntityManagerInterface $em,
        protected UserRepository $userRepository,
        protected FriendshipRepository $friendshipRepository,
    ) {
    }

    public function handleCreate(FriendshipDTO $dto): FriendshipDTO
    {
        $this->friendshipTransformer->setDTO($dto);
        $friendship = $this->friendshipTransformer->mapDTOToEntity();

        $friendship = $this->friendshipManager->initAuthenticatedUser($friendship);
        $friendship = $this->friendshipManager->initFriendUser($dto->receiver->id, $friendship);

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
     * @return \ArrayObject<int, FriendshipDTO>
     */
    public function handleGetPendingFriendships(string $type): \ArrayObject
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $pendingList = $this->friendshipRepository->findPendingFriendshipsByUserIdAndType($userId, $type);
        $pendingCollection = $this->friendshipListTransformer->transformArrayToObjectList($pendingList);

        $this->friendshipListTransformer->setEntityList($pendingCollection);

        return $this->friendshipListTransformer->mapEntityListToDTOList();
    }
}

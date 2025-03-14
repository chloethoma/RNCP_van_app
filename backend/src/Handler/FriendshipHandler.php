<?php

namespace App\Handler;

use App\DataTransformer\FriendshipDataTransformer;
use App\DTO\Friendship\FriendshipDTO;
use App\Manager\FriendshipManager;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FriendshipHandler
{
    public function __construct(
        protected UserManager $userManager,
        protected FriendshipManager $friendshipManager,
        protected FriendshipDataTransformer $friendshipTransformer,
        protected EntityManagerInterface $em,
    ) {
    }

    public function handleCreate(FriendshipDTO $dto): FriendshipDTO
    {
        $this->friendshipTransformer->setDTO($dto);
        $friendship = $this->friendshipTransformer->mapDTOToEntity();

        $friendship = $this->friendshipManager->initOwner($friendship);
        $friendship = $this->friendshipManager->initFriendUser($dto->receiverId, $friendship);

        if (!$this->friendshipManager->isReceiverIdDifferentFromCurrentUser($friendship->getRequester()->getId(), $friendship->getReceiver()->getId())) {
            throw new BadRequestHttpException();
        }

        $this->friendshipManager->checkIfFriendshipAlreadyExists($friendship);

        $this->em->persist($friendship);
        $this->em->flush();

        $this->friendshipTransformer->setEntity($friendship);

        return $this->friendshipTransformer->mapEntityToDTO();
    }
}

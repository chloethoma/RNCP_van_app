<?php

namespace App\Manager;

use App\Entity\Spot;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Services\Exceptions\Spot\SpotAccessDeniedException;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class SpotManager
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserManager $userManager,
        protected FriendshipRepository $friendshipRepository,
    ) {
    }

    /**
     * @throws UnauthenticatedUserException
     * @throws UserNotFoundException
     */
    public function initSpotOwner(Spot $spot): Spot
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $userRepository = $this->em->getRepository(User::class);
        $owner = $userRepository->find($userId);

        if (!$owner) {
            throw new UserNotFoundException();
        }

        $spot->setOwner($owner);

        return $spot;
    }

    /**
     * @throws UnauthenticatedUserException
     * @throws SpotAccessDeniedException
     */
    public function checkAccess(Spot $spot): void
    {
        if ($spot->getOwner()->getId() !== $this->userManager->getAuthenticatedUserId()) {
            throw new SpotAccessDeniedException();
        }
    }

    /**
     * @throws UnauthenticatedUserException
     * @throws SpotAccessDeniedException
     */
    public function checkSpotFriendAccess(Spot $spot): void
    {
        $spotOwnerId = $spot->getOwner()->getId();
        $userId = $this->userManager->getAuthenticatedUserId();

        if (!$this->friendshipRepository->isfriendshipExist($spotOwnerId, $userId)) {
            throw new SpotAccessDeniedException();
        }
    }
}

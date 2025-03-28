<?php

namespace App\Manager;

use App\Entity\Spot;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpotManager
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserManager $userManager,
        protected FriendshipRepository $friendshipRepository,
    ) {
    }

    public function initSpotOwner(Spot $spot): Spot
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $userRepository = $this->em->getRepository(User::class);
        $owner = $userRepository->find($userId);

        if (!$owner) {
            throw new NotFoundHttpException();
        }

        $spot->setOwner($owner);

        return $spot;
    }

    public function checkAccess(Spot $spot): void
    {
        if ($spot->getOwner()->getId() !== $this->userManager->getAuthenticatedUserId()) {
            throw new AccessDeniedHttpException();
        }
    }

    public function checkSpotFriendAccess(Spot $spot): void
    {
        $spotOwnerId = $spot->getOwner()->getId();
        $userId = $this->userManager->getAuthenticatedUserId();

        if (!$this->friendshipRepository->isfriendshipExist($spotOwnerId, $userId)) {
            throw new AccessDeniedHttpException();
        }
    }
}

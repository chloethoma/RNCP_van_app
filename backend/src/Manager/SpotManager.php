<?php

namespace App\Manager;

use App\Entity\Spot;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpotManager
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected Security $security,
    ) {
    }

    public function initSpotOwner(Spot $spot): Spot
    {
        $userId = $this->getOwner();

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
        if ($spot->getOwner()->getId() !== $this->getOwner()) {
            throw new AccessDeniedHttpException();
        }
    }

    private function getOwner(): int
    {
        return (int) $this->security->getUser()->getUserIdentifier();
    }
}

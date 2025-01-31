<?php

namespace App\Service\Manager;

use App\Entity\Spot;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpotManager
{
    public function __construct(
        protected JWTTokenManagerInterface $jwtManager,
        protected TokenStorageInterface $tokenStorageInterface,
        protected EntityManagerInterface $em,
        protected Security $security,
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function initSpotOwner(Spot $spot): Spot
    {
        $userId = $this->security->getUser()->getUserIdentifier();

        $userRepository = $this->em->getRepository(User::class);
        $owner = $userRepository->find($userId);

        if (!$owner) {
            throw new NotFoundHttpException();
        }

        $spot->setOwner($owner);

        return $spot;
    }
}

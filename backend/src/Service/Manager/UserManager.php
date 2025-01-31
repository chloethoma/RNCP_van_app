<?php

namespace App\Service\Manager;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserManager
{
    public function __construct(
        protected JWTTokenManagerInterface $jwtManager,
        protected TokenStorageInterface $tokenStorageInterface,
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function createFromPayload(array $payload)
    {
        $user = new User();
        $user->setId($payload['id']);
        $user->setEmail($payload['email']);

        return $user;
    }
}

<?php

namespace App\Service\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserManager
{
    public function __construct(
        protected JWTTokenManagerInterface $jwtManager,
        protected TokenStorageInterface $tokenStorageInterface,
        protected UserPasswordHasherInterface $passwordHasher,
        protected UserRepository $userRepository,
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function hashPassword(User $user, string $plainPassword): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        return $user;
    }

    public function checkIfUserAlreadyExists(User $user): void
    {
        if ($this->userRepository->findOneByEmail($user)) {
            throw new ConflictHttpException('User already exists with this email');
        }

        if ($this->userRepository->findOneByPseudo($user)) {
            throw new ConflictHttpException('This pseudo already exists');
        }
    }

    public function createToken(User $user): User
    {
        $token = $this->jwtManager->create($user);
        $user->setToken($token);

        return $user;
    }
}

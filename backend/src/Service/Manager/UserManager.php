<?php

namespace App\Service\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserManager
{
    public function __construct(
        protected JWTTokenManagerInterface $jwtManager,
        protected TokenStorageInterface $tokenStorageInterface,
        protected UserPasswordHasherInterface $passwordHasher,
        protected UserRepository $userRepository,
        protected Security $security,
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

    public function isEmailAlreadyTaken(User $user): bool
    {
        $existingUser = $this->userRepository->findByEmail($user);

        return null !== $existingUser && $existingUser->getId() !== $user->getId();
    }

    public function isPseudoAlreadyTaken(User $user): bool
    {
        $existingUser = $this->userRepository->findByPseudo($user);

        return null !== $existingUser && $existingUser->getId() !== $user->getId();
    }

    public function createToken(User $user): User
    {
        $token = $this->jwtManager->create($user);
        $user->setToken($token);

        return $user;
    }

    public function getUserIdFromToken(): int
    {
        return (int) $this->security->getUser()->getUserIdentifier();
    }

    public function checkAccess(User $user): bool
    {
        if ($user->getId() !== $this->getOwner()) {
            return false;
        }

        return true;
    }

    private function getOwner(): int
    {
        return (int) $this->security->getUser()->getUserIdentifier();
    }
}

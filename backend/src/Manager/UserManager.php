<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        protected Security $security,
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function getOwner(): int
    {
        return (int) $this->security->getUser()->getUserIdentifier();
    }

    public function hashPassword(User $user, string $plainPassword): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        return $user;
    }

    public function checkEmailOrPseudoAlreadyTaken(User $user): void
    {
        $errors = [];

        if ($this->isEmailAlreadyTaken($user)) {
            $errors[] = 'User already exists with this email';
        }

        if ($this->isPseudoAlreadyTaken($user)) {
            $errors[] = 'User already exists with this pseudo';
        }

        if (!empty($errors)) {
            throw new ConflictHttpException(implode(' | ', $errors));
        }
    }

    public function checkCurrentPasswordValidity(User $user, string $currentPassword): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            throw new AccessDeniedHttpException();
        }
    }

    public function createToken(User $user): User
    {
        $token = $this->jwtManager->create($user);
        $user->setToken($token);

        return $user;
    }

    private function isEmailAlreadyTaken(User $user): bool
    {
        $existingUser = $this->userRepository->findByEmail($user->getUserIdentifier());

        return null !== $existingUser && $existingUser->getId() !== $user->getId();
    }

    private function isPseudoAlreadyTaken(User $user): bool
    {
        $existingUser = $this->userRepository->findByPseudo($user->getPseudo());

        return null !== $existingUser && $existingUser->getId() !== $user->getId();
    }
}

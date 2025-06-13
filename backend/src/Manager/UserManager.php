<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserAccessDeniedException;
use App\Services\Exceptions\User\UserConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
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

    /**
     * Get user identity (userId) from token.
     *
     * @return int UserIdentifier from token (userId)
     *
     * @throws UnauthenticatedUserException
     */
    public function getAuthenticatedUserId(): int
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new UnauthenticatedUserException();
        }

        return (int) $user->getUserIdentifier();
    }

    /**
     * Get User entity associated with the authenticated user's identifier.
     *
     * This method first ensures the user is authenticated (via getAuthenticatedUserId),
     * then fetches the corresponding User entity from the database.
     *
     * @return User User entity from database
     *
     * @throws UserNotFoundException
     * @throws UnauthenticatedUserException
     */
    public function getAuthenticatedUser(): User
    {
        $user = $this->userRepository->findByUserIdentifier($this->getAuthenticatedUserId());

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function hashPassword(User $user, string $plainPassword): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        return $user;
    }

    /**
     * @throws UserConflictException
     */
    public function checkEmailOrPseudoAlreadyTaken(User $user): void
    {
        $errors = [];

        if ($this->isEmailAlreadyTaken($user)) {
            $errors[] = 'email';
        }

        if ($this->isPseudoAlreadyTaken($user)) {
            $errors[] = 'pseudo';
        }

        if (!empty($errors)) {
            throw new UserConflictException($errors);
        }
    }

    /**
     * @throws UserAccessDeniedException Current password is incorrect
     */
    public function checkCurrentPasswordValidity(User $user, string $currentPassword): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            throw new UserAccessDeniedException('Current password is incorrect');
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
        $existingUser = $this->userRepository->findByEmail($user->getEmail());

        return null !== $existingUser && $existingUser->getId() !== $user->getId();
    }

    private function isPseudoAlreadyTaken(User $user): bool
    {
        $existingUser = $this->userRepository->findByPseudo($user->getPseudo());

        return null !== $existingUser && $existingUser->getId() !== $user->getId();
    }
}

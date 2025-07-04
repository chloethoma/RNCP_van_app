<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserAccessDeniedException;
use App\Services\Exceptions\User\UserConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserManagerTest extends TestCase
{
    private const EMAIL = 'test@example.com';
    private const PSEUDO = 'testpseudo';
    private const PASSWORD = 'password';
    private const WRONG_PASSWORD = 'wrongpassword';
    private const EXISTING_USER_ID = 99;

    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private UserManager $userManager;
    private Security $security;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->security = $this->createMock(Security::class);

        $jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        $this->userManager = new UserManager(
            $jwtManager,
            $tokenStorage,
            $this->passwordHasher,
            $this->userRepository,
            $this->security
        );
    }

    public function testGetAuthenticatedUserSuccess(): void
    {
        $expectedUser = $this->createMock(User::class);
        $expectedUser
            ->method('getUserIdentifier')
            ->willReturn(self::EMAIL);

        $this->security
            ->method('getUser')
            ->willReturn($expectedUser);

        $this->userRepository
            ->method('findByUserIdentifier')
            ->with(self::EMAIL)
            ->willReturn($expectedUser);

        $actualUser = $this->userManager->getAuthenticatedUser();

        $this->assertSame($expectedUser, $actualUser);
    }

    public function testGetAuthenticatedUserExceptionUnauthenticated(): void
    {
        $this->security
            ->method('getUser')
            ->willReturn(null);

        $this->expectException(UnauthenticatedUserException::class);

        $this->userManager->getAuthenticatedUser();
    }

    public function testGetAuthenticatedUserThrowsWhenUserNotFound(): void
    {
        $user = new User();
        $user->setEmail(self::EMAIL);
        $user->setPseudo(self::PSEUDO);

        $this->security
            ->method('getUser')
            ->willReturn($user);

        $this->userRepository
            ->method('findByEmail')
            ->with(self::EMAIL)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->userManager->getAuthenticatedUser();
    }

    public function testCheckEmailOrPseudoAlreadyTakenNoConflict(): void
    {
        $user = new User();
        $user->setEmail(self::EMAIL);
        $user->setPseudo(self::PSEUDO);

        $this->userRepository->method('findByEmail')->willReturn(null);
        $this->userRepository->method('findByPseudo')->willReturn(null);

        $this->userManager->checkEmailOrPseudoAlreadyTaken($user);

        $this->assertTrue(true);
    }

    public function testCheckEmailAlreadyTaken(): void
    {
        $this->expectException(UserConflictException::class);

        $user = new User();
        $user->setEmail(self::EMAIL);
        $user->setPseudo(self::PSEUDO);

        $existingUser = new User();
        $existingUser->setId(self::EXISTING_USER_ID);

        $this->userRepository->method('findByEmail')->willReturn($existingUser);
        $this->userRepository->method('findByPseudo')->willReturn(null);

        $this->userManager->checkEmailOrPseudoAlreadyTaken($user);
    }

    public function testCheckPseudoAlreadyTaken(): void
    {
        $this->expectException(UserConflictException::class);

        $user = new User();
        $user->setEmail(self::EMAIL);
        $user->setPseudo(self::PSEUDO);

        $existingUser = new User();
        $existingUser->setId(self::EXISTING_USER_ID);

        $this->userRepository->method('findByEmail')->willReturn(null);
        $this->userRepository->method('findByPseudo')->willReturn($existingUser);

        $this->userManager->checkEmailOrPseudoAlreadyTaken($user);
    }

    public function testCheckBothEmailAndPseudoAlreadyTaken(): void
    {
        $this->expectException(UserConflictException::class);

        $user = new User();
        $user->setEmail(self::EMAIL);
        $user->setPseudo(self::PSEUDO);

        $existingUser = new User();
        $existingUser->setId(self::EXISTING_USER_ID);

        $this->userRepository->method('findByEmail')->willReturn($existingUser);
        $this->userRepository->method('findByPseudo')->willReturn($existingUser);

        $this->userManager->checkEmailOrPseudoAlreadyTaken($user);
    }

    public function testCheckCurrentPasswordValidityValidPassword(): void
    {
        $user = new User();

        $this->passwordHasher
            ->method('isPasswordValid')
            ->with($user, self::PASSWORD)
            ->willReturn(true);

        $this->userManager->checkCurrentPasswordValidity($user, self::PASSWORD);

        $this->assertTrue(true);
    }

    public function testCheckCurrentPasswordValidityInvalidPassword(): void
    {
        $this->expectException(UserAccessDeniedException::class);

        $user = new User();

        $this->passwordHasher
            ->method('isPasswordValid')
            ->with($user, self::WRONG_PASSWORD)
            ->willReturn(false);

        $this->userManager->checkCurrentPasswordValidity($user, self::WRONG_PASSWORD);
    }
}

<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Services\Exceptions\User\UserAccessDeniedException;
use App\Services\Exceptions\User\UserConflictException;
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

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $security = $this->createMock(Security::class);

        $this->userManager = new UserManager(
            $jwtManager,
            $tokenStorage,
            $this->passwordHasher,
            $this->userRepository,
            $security
        );
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

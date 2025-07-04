<?php

namespace App\Tests\Integration\Handler;

use App\DTO\User\UserDTO;
use App\DTO\User\UserPasswordDTO;
use App\DTO\User\UserSummaryDTO;
use App\Entity\User;
use App\Handler\UserHandler;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserAccessDeniedException;
use App\Services\Exceptions\User\UserConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserHandlerTest extends KernelTestCase
{
    private const EXISTING_USER_EMAIL = 'user1@example.com';
    private const EXISTING_USER_PSEUDO = 'User1';
    private const EXISTING_USER_PASSWORD = 'password1';
    private const EXISTING_OTHER_USER_EMAIL = 'other_user12@example.com';
    private const EXISTING_OTHER_USER_PSEUDO = 'OtherUser12';
    private const NON_EXISTING_USER_EMAIL = 'ghost@example.com';
    private const NON_EXISTING_USER_PSEUDO = 'Ghost';
    private const NEW_PASSWORD = 'test_password!';
    private const UPDATED_PSEUDO = 'UpdatedPseudo';

    private UserHandler $handler;
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->handler = $container->get(UserHandler::class);
        $this->em = $container->get(EntityManagerInterface::class);
        $this->tokenStorage = $container->get(TokenStorageInterface::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    // Test the unauthenticated use case juste once (with handleGet)
    public function testUnauthenticatedUserException(): void
    {
        $this->tokenStorage->setToken(null);

        $this->expectException(UnauthenticatedUserException::class);

        $this->handler->handleGet();
    }

    public function testHandleCreateSuccess(): void
    {
        $dto = new UserDTO(
            id: null,
            email: self::NON_EXISTING_USER_EMAIL,
            emailVerified: true,
            password: self::NEW_PASSWORD,
            pseudo: self::NON_EXISTING_USER_PSEUDO,
            createdAt: null,
            updatedAt: null,
            picture: null,
            token: null
        );

        $createdUserDTO = $this->handler->handleCreate($dto);

        $this->assertNotNull($createdUserDTO->id);
        $this->assertSame(self::NON_EXISTING_USER_EMAIL, $createdUserDTO->email);
        $this->assertNotNull($createdUserDTO->token);
        $this->assertNotNull($createdUserDTO->createdAt);
        $this->assertNotNull($createdUserDTO->updatedAt);
        $this->assertNotEquals(self::NEW_PASSWORD, $createdUserDTO->password);

        $user = $this->em->getRepository(User::class)->find($createdUserDTO->id);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(self::NON_EXISTING_USER_PSEUDO, $user->getPseudo());
    }

    public function testHandleCreateExceptionWithExistingEmail(): void
    {
        $dto = new UserDTO(
            id: null,
            email: self::EXISTING_USER_EMAIL,
            emailVerified: true,
            password: self::NEW_PASSWORD,
            pseudo: self::UPDATED_PSEUDO,
            createdAt: null,
            updatedAt: null,
            picture: null,
            token: null
        );

        $this->expectException(UserConflictException::class);

        $this->handler->handleCreate($dto);
    }

    public function testHandleCreateExceptionWithExistingPseudo(): void
    {
        $dto = new UserDTO(
            id: null,
            email: self::NON_EXISTING_USER_EMAIL,
            emailVerified: true,
            password: self::NEW_PASSWORD,
            pseudo: self::EXISTING_USER_PSEUDO,
            createdAt: null,
            updatedAt: null,
            picture: null,
            token: null
        );

        $this->expectException(UserConflictException::class);

        $this->handler->handleCreate($dto);
    }

    public function testHandleGetSuccess(): void
    {
        $user = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $userDTO = $this->handler->handleGet();

        $this->assertSame($user->getEmail(), $userDTO->email);
        $this->assertSame($user->getPseudo(), $userDTO->pseudo);
    }

    public function testHandleUpdateSuccess(): void
    {
        $user = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $dto = new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            emailVerified: true,
            password: $user->getPassword(),
            pseudo: self::UPDATED_PSEUDO,
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt(),
            picture: $user->getPicture(),
            token: null
        );

        $updatedDTO = $this->handler->handleUpdate($dto);

        $this->assertSame($dto->pseudo, $updatedDTO->pseudo);
        $this->assertSame($user->getEmail(), $updatedDTO->email);
        $this->assertNotEquals($dto->updatedAt, $updatedDTO->updatedAt);
    }

    public function testHandleUpdateExceptionWithExistingPseudo(): void
    {
        $user = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $dto = new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            emailVerified: true,
            password: $user->getPassword(),
            pseudo: self::EXISTING_OTHER_USER_PSEUDO,
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt(),
            picture: $user->getPicture(),
            token: null
        );

        $this->expectException(UserConflictException::class);

        $this->handler->handleUpdate($dto);
    }

    public function testHandleUpdateExceptionWithExistingEmail(): void
    {
        $user = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $dto = new UserDTO(
            id: $user->getId(),
            email: self::EXISTING_OTHER_USER_EMAIL,
            emailVerified: true,
            password: $user->getPassword(),
            pseudo: $user->getPseudo(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt(),
            picture: $user->getPicture(),
            token: null
        );

        $this->expectException(UserConflictException::class);

        $this->handler->handleUpdate($dto);
    }

    public function testHandleUpdatePasswordSuccess(): void
    {
        $user = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $dto = new UserPasswordDTO(
            currentPassword: self::EXISTING_USER_PASSWORD,
            newPassword: self::NEW_PASSWORD
        );

        $this->handler->handleUpdatePassword($dto);

        $updatedUser = $this->em->getRepository(User::class)->findOneBy(['email' => self::EXISTING_USER_EMAIL]);

        $this->assertTrue($this->passwordHasher->isPasswordValid($updatedUser, self::NEW_PASSWORD));
    }

    public function testHandleUpdatePasswordAccessDeniedException(): void
    {
        $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $dto = new UserPasswordDTO(
            currentPassword: 'dummy',
            newPassword: self::NEW_PASSWORD
        );

        $this->expectException(UserAccessDeniedException::class);

        $this->handler->handleUpdatePassword($dto);
    }

    public function testHandleDeleteSuccess(): void
    {
        $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $this->handler->handleDelete();

        $deletedUser = $this->em->getRepository(User::class)->findOneBy(['email' => self::EXISTING_USER_EMAIL]);

        $this->assertEquals(null, $deletedUser);
    }

    public function testHandleSearchUserSuccess(): void
    {
        $currentUser = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $result = $this->handler->handleSearchUser('User');

        foreach ($result as $user) {
            $this->assertStringContainsString('User', $user->pseudo);
            $this->assertNotSame($currentUser->getPseudo(), $user->pseudo);
            $this->assertStringNotContainsString('OtherUser', $user->pseudo);
        }
    }

    public function testHandleSearchUserReturnsEmptyListWhenNoMatch(): void
    {
        $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $result = $this->handler->handleSearchUser('InexistantPseudo123');

        $this->assertCount(0, $result);
    }

    public function testHandleGetUsersSummary(): void
    {
        $user = $this->authenticateTestUser(self::EXISTING_USER_EMAIL);

        $result = $this->handler->handleGetUserSummary();

        $this->assertInstanceOf(UserSummaryDTO::class, $result);
    }

    public function testUserNotFoundException(): void
    {
        $user = new User();
        $user->setEmail(self::NON_EXISTING_USER_EMAIL);
        $user->setPassword(self::NEW_PASSWORD);
        $user->setPseudo(self::NON_EXISTING_USER_PSEUDO);

        $token = new UsernamePasswordToken($user, 'main');
        $this->tokenStorage->setToken($token);

        $this->expectException(UserNotFoundException::class);

        $this->handler->handleGet();
    }

    private function authenticateTestUser(string $userEmail): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userEmail]);
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main'));

        return $user;
    }
}

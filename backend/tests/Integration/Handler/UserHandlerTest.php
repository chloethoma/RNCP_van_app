<?php

namespace App\Tests\Integration\Handler;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Handler\UserHandler;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserHandlerTest extends KernelTestCase
{
    private UserHandler $handler;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->handler = $container->get(UserHandler::class);
        $this->em = $container->get(EntityManagerInterface::class);
    }

    public function testHandleCreate(): void
    {
        $dto = new UserDTO(
            id: null,
            email: 'user@example.com',
            emailVerified: true,
            password: 'password',
            pseudo: 'user',
            createdAt: null,
            updatedAt: null,
            picture: null,
            token: null
        );

        $createdUserDTO = $this->handler->handleCreate($dto);

        $this->assertNotNull($createdUserDTO->id);
        $this->assertSame('user@example.com', $createdUserDTO->email);
        $this->assertNotNull($createdUserDTO->token);
        $this->assertNotNull($createdUserDTO->createdAt);
        $this->assertNotNull($createdUserDTO->updatedAt);
        $this->assertNotEquals('password', $createdUserDTO->password);

        $user = $this->em->getRepository(User::class)->find($createdUserDTO->id);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('user', $user->getPseudo());
    }

    public function testHandleCreateWithExistingEmail(): void
    {
        $dto = new UserDTO(
            id: null,
            email: 'alice@example.com',
            emailVerified: true,
            password: 'password',
            pseudo: 'UniquePseudo',
            createdAt: null,
            updatedAt: null,
            picture: null,
            token: null
        );

        $this->expectException(UserConflictException::class);
        $this->expectExceptionMessage('User already exists');

        $this->handler->handleCreate($dto);
    }

    public function testHandleCreateWithExistingPseudo(): void
    {
        $dto = new UserDTO(
            id: null,
            email: 'uniqueemail@example.com',
            emailVerified: true,
            password: 'password',
            pseudo: 'Alice',
            createdAt: null,
            updatedAt: null,
            picture: null,
            token: null
        );

        $this->expectException(UserConflictException::class);
        $this->expectExceptionMessage('User already exists');

        $this->handler->handleCreate($dto);
    }

    // public function testHandleGetSuccess(): void
    // {
    //     $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'alice@example.com']);

    //     $identifier = $user->getId();

    //     $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
    //     self::getContainer()->get(TokenStorageInterface::class)->setToken($token);
    //     dd($token->getUserIdentifier());

    //     // Assertion for ID used in the repository lookup
    //     $foundUser = $this->em->getRepository(User::class)->findByUserIdentifier((int) $identifier);
    //     $this->assertSame(
    //         $user,
    //         $foundUser,
    //         'User not found using getUserIdentifier() TEST'
    //     );

    //     $userDTO = $this->handler->handleGet();
    //     dd($userDTO);

    //     $this->assertSame($user->getEmail(), $userDTO->email);
    //     $this->assertSame($user->getPseudo(), $userDTO->pseudo);
    // }

    // public function testHandleGetThrowsUserNotFoundException(): void
    // {
    //     $user = clone $this->em->getRepository(User::class)->findOneBy(['email' => 'alice@example.com']);
    //     $this->em->remove($user);
    //     $this->em->flush();

    //     $token = new UsernamePasswordToken($user, 'password', 'main', $user->getRoles());
    //     $this->security->setToken($token);

    //     $this->expectException(UserNotFoundException::class);

    //     $this->handler->handleGet();
    // }

    // public function testHandleGetThrowsUnauthenticatedUserException(): void
    // {
    //     $this->security->setToken(null);

    //     $this->expectException(UnauthenticatedUserException::class);

    //     $this->handler->handleGet();
    // }
}

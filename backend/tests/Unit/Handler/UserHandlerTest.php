<?php

namespace App\Tests\Unit\Handler;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\Handler\UserHandler;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserHandlerTest extends KernelTestCase
{
    private UserHandler $handler;
    private UserDataTransformer $transformer;
    private $managerMock;
    private $repositoryMock;

    public function setup(): void
    {
        self::bootKernel();

        $this->repositoryMock = $this->createMock(UserRepository::class);
        $this->managerMock = $this->createMock(UserManager::class);

        $container = self::getContainer();

        $this->transformer = $container->get(UserDataTransformer::class);

        $this->handler = new UserHandler(
            $this->transformer,
            $this->managerMock,
            $this->repositoryMock
        );
    }

    public function testHandleCreate(): void
    {
        $userInputDTO = new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'test',
            password:'password'
        );

        $this->managerMock
            ->expects($this->once())
            ->method('checkEmailOrPseudoAlreadyTaken');

        $this->managerMock
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn($this->getUserEntity());

        $this->repositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->getUserEntity());
        
        $this->managerMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn($this->getUserEntity());
        
        $userOutputDTO = $this->handler->handleCreate($userInputDTO);
        
        $expectedDTO = $this->getUserDTO();

        $this->assertEquals($expectedDTO, $userOutputDTO);
    }

    public function testHandleCreateConflictException(): void 
    {
        $userInputDTO = new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'test',
            password:'password'
        );

        $this->managerMock
        ->expects($this->once())
        ->method('checkEmailOrPseudoAlreadyTaken')
        ->willThrowException(new ConflictHttpException());

        $this->expectException(ConflictHttpException::class);

        $this->handler->handleCreate($userInputDTO);
    }

    public function testHandleGet(): void
    {
        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
            ->expects($this->once())
            ->method('checkAccess');
        
        $userOutputDTO = $this->handler->handleGet(1);

        $expectedDTO = $this->getUserDTO();

        $this->assertEquals($expectedDTO, $userOutputDTO);
    }

    public function testHandleGetNotFoundException(): void
    {
        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleGet(1);
    }

    public function testHandleGetAccessDeniedException(): void
    {
        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
        ->expects($this->once())
        ->method('checkAccess')
        ->willThrowException(new AccessDeniedHttpException());

        $this->expectException(AccessDeniedHttpException::class);

        $this->handler->handleGet(1);
    }

    public function testHandleUpdate(): void
    {
        $userInputDTO = new UserDTO(
            email: 'test@gmail.com',
            emailVerified: false,
            pseudo: 'test',
            picture: null,
            password:'password'
        );

        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
        ->expects($this->once())
        ->method('checkAccess');

        $this->managerMock
        ->expects($this->once())
        ->method('checkEmailOrPseudoAlreadyTaken');

        $this->repositoryMock
            ->expects($this->once())
            ->method('update')
            ->willReturn($this->getUserEntity());
        
        $userOutputDTO = $this->handler->handleUpdate(1, $userInputDTO);

        $expectedDTO = $this->getUserDTO();

        $this->assertEquals($expectedDTO, $userOutputDTO);
    }

    public function testHandleUpdateNotFoundException(): void
    {
        $userInputDTO = new UserDTO(
            email: 'test@gmail.com',
            emailVerified: false,
            pseudo: 'test',
            picture: null,
            password:'password'
        );

        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleUpdate(1, $userInputDTO);
    }

    public function testHandleUpdateAccessDeniedException(): void
    {
        $userInputDTO = new UserDTO(
            email: 'test@gmail.com',
            emailVerified: false,
            pseudo: 'test',
            picture: null,
            password:'password'
        );

        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
        ->expects($this->once())
        ->method('checkAccess')
        ->willThrowException(new AccessDeniedHttpException());

        $this->expectException(AccessDeniedHttpException::class);

        $this->handler->handleUpdate(1, $userInputDTO);
    }

    public function testHandleUpdateConflictException(): void
    {
        $userInputDTO = new UserDTO(
            email: 'test@gmail.com',
            emailVerified: false,
            pseudo: 'test',
            picture: null,
            password:'password'
        );

        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
        ->expects($this->once())
        ->method('checkAccess');

        $this->managerMock
        ->expects($this->once())
        ->method('checkEmailOrPseudoAlreadyTaken')
        ->willThrowException(new ConflictHttpException());

        $this->expectException(ConflictHttpException::class);

        $this->handler->handleUpdate(1, $userInputDTO);
    }

    public function testHandleDelete(): void
    {
        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
        ->expects($this->once())
        ->method('checkAccess');

        $this->repositoryMock
        ->expects($this->once())
        ->method('delete')
        ->with($this->getUserEntity());

        $this->handler->handleDelete(1);
    }

    public function testHandleDeleteNotFoundException(): void
    {
        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleGet(1);
    }

    public function testHandleDeleteAccessDeniedException(): void
    {
        $this->repositoryMock
        ->expects($this->once())
        ->method('findByUserIdentifier')
        ->with(1)
        ->willReturn($this->getUserEntity());

        $this->managerMock
        ->expects($this->once())
        ->method('checkAccess')
        ->willThrowException(new AccessDeniedHttpException());

        $this->expectException(AccessDeniedHttpException::class);

        $this->handler->handleDelete(1);
    }

    private function getUserEntity(): User
    {
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setEmailVerified(false);
        $user->setPseudo('test');
        $user->setPassword('password');
        $user->setPicture(null);
        $user->setCreatedAt(new \DateTimeImmutable('2025-02-18T15:51:08+00:00'));
        $user->setUpdatedAt(new \DateTime('2025-02-18T15:53:42+00:00'));
        $user->setId(1);
        $user->setToken('jwt');

        return $user;
    }

    private function getUserDTO(): UserDTO
    {
        return new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'test',
            password: null,
            id: 1,
            emailVerified: false,
            createdAt: new \DateTime('2025-02-18T15:51:08+00:00'),
            updatedAt: new \DateTime('2025-02-18T15:53:42+00:00'),
            picture: null,
            token: 'jwt'
        );
    }
}

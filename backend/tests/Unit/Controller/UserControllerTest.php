<?php

namespace App\Tests\Unit\Controller;

use App\Controller\UserController;
use App\DTO\User\UserDTO;
use App\Handler\UserHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class UserControllerTest extends KernelTestCase
{
    private UserController $controller;
    private $loggerMock;
    private $handlerMock;
    private SerializerInterface $serializer;

    public function setUp(): void
    {
        self::bootKernel();

        $this->handlerMock = $this->createMock(UserHandler::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->serializer = self::getContainer()->get(SerializerInterface::class);

        $this->controller = new UserController(
            $this->loggerMock,
            $this->handlerMock,
        );

        $this->controller->setContainer(self::getContainer());
    }

    public function testCreateUser(): void
    {
        $userDTO = new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'pseudo',
            password: 'test1234'
        );

        $expectedDTO = $this->getUserDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleCreate')
            ->with($userDTO)
            ->willReturn($expectedDTO);

        $response = $this->controller->createUser($userDTO);
        $expectedResponse = $this->serializer->serialize($expectedDTO, 'json', ['groups' => ['read']]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($expectedResponse, $response->getContent());
    }

    #[DataProvider('createUserExceptionDataProvider')]
    public function testCreateUserException(\Throwable $exception, int $expectedStatusCode): void
    {
        $userDTO = new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'pseudo',
            password: 'test1234'
        );

        $this->handlerMock
            ->expects($this->once())
            ->method('handleCreate')
            ->with($userDTO)
            ->willThrowException($exception);

        $response = $this->controller->createUser($userDTO);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function createUserExceptionDataProvider()
    {
        return [
            'Conflict' => [new ConflictHttpException(), 409],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testGetUserIdentity(): void
    {
        $expectedDTO = $this->getUserDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleGet')
            ->with(1)
            ->willReturn($expectedDTO);

        $response = $this->controller->getUserIdentity(1);
        $expectedResponse = $this->serializer->serialize($expectedDTO, 'json', ['groups' => ['read']]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, $response->getContent());
    }

    #[DataProvider('getUserIdentityExceptionDataProvider')]
    public function testGetUserIdentityException(\Throwable $exception, int $expectedStatusCode): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleGet')
            ->with(1)
            ->willThrowException($exception);

        $response = $this->controller->getUserIdentity(1);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function getUserIdentityExceptionDataProvider()
    {
        return [
            'Not found' => [new NotFoundHttpException(), 404],
            'AccessDenied' => [new AccessDeniedHttpException(), 403],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testUpdateUser(): void
    {
        $userDTO = new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'pseudo',
            emailVerified: false,
            picture: null
        );

        $expectedDTO = $this->getUserDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleUpdate')
            ->with(1, $userDTO)
            ->willReturn($expectedDTO);

        $response = $this->controller->updateUser($userDTO, 1);
        $expectedResponse = $this->serializer->serialize($expectedDTO, 'json', ['groups' => ['read']]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, $response->getContent());
    }

    #[DataProvider('updateUserExceptionDataProvider')]
    public function testUpdateUserException(\Throwable $exception, int $expectedStatusCode): void
    {
        $userDTO = new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'pseudo',
            emailVerified: false,
            picture: null
        );

        $this->handlerMock
            ->expects($this->once())
            ->method('handleUpdate')
            ->with(1, $userDTO)
            ->willThrowException($exception);

        $response = $this->controller->updateUser($userDTO, 1);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function updateUserExceptionDataProvider()
    {
        return [
            'Not found' => [new NotFoundHttpException(), 404],
            'AccessDenied' => [new AccessDeniedHttpException(), 403],
            'Conflict' => [new ConflictHttpException(), 409],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testDeleteUser(): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleDelete')
            ->with(1);

        $response = $this->controller->deleteUser(1);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('null', $response->getContent());
    }

    #[DataProvider('deleteUserExceptionDataProvider')]
    public function testDeleteUserException(\Throwable $exception, int $expectedStatusCode): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleDelete')
            ->with(1)
            ->willThrowException($exception);

        $response = $this->controller->deleteUser(1);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function deleteUserExceptionDataProvider()
    {
        return [
            'Not found' => [new NotFoundHttpException(), 404],
            'AccessDenied' => [new AccessDeniedHttpException(), 403],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    private function getUserDTO(): UserDTO
    {
        return new UserDTO(
            email: 'test@gmail.com',
            pseudo: 'pseudo',
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

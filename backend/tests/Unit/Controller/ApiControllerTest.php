<?php

namespace App\Controller;

use App\DTO\User\UserDTO;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ApiControllerTest extends KernelTestCase
{
    private ApiController $controller;
    private $loggerMock;
    private SerializerInterface $serializer;

    public function setUp(): void
    {
        self::bootKernel();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->serializer = self::getContainer()->get(SerializerInterface::class);
        $this->controller = new ApiController(
            $this->loggerMock,
        );

        $this->controller->setContainer(self::getContainer());
    }

    public function testLogException(): void
    {
        $location = 'App\Controller\TestMethod';
        $exception = new \Exception('Test error message');
        $details = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
        ];

        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with($location, $details);

        $this->controller->logException($location, $exception);
    }

    public function testHandleException()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'ServerError',
                'message' => 'Oops ! Something went wrong.',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->handleException(new \Exception(), 'User controller');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());
    }

    public function testServeOkResponse(): void
    {
        $content = new UserDTO(
            id: 1,
            email: 'test@gmail.com',
            pseudo: 'test',
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
            picture: 'test',
            token: 'test',
            emailVerified: false,
            password: 'test1234',
        );

        $expectedResponse = $this->serializer->serialize($content, 'json', ['groups' => ['read']]);

        $response = $this->controller->serveOkResponse(content: $content, groups: ['read']);

        $this->assertJsonResponse($response, Response::HTTP_OK, $expectedResponse);
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    public function testServeCreatedResponse(): void
    {
        $content = new UserDTO(
            id: 1,
            email: 'test@gmail.com',
            pseudo: 'test',
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
            picture: 'test',
            token: 'test',
            emailVerified: false,
            password: 'test1234',
        );

        $location = '/users/1';
        $expectedResponse = $this->serializer->serialize($content, 'json', ['groups' => ['read']]);

        $response = $this->controller->serveCreatedResponse($content, $location, ['read']);

        $this->assertJsonResponse($response, Response::HTTP_CREATED, $expectedResponse);
        $this->assertEquals($location, $response->headers->get('Location'));
    }

    public function testServeNoContentResponse()
    {
        $response = $this->controller->serveNoContentResponse();

        $this->assertJsonResponse($response, Response::HTTP_NO_CONTENT, 'null');
    }

    public function testServeNotFoundResponse()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'NotFound',
                'message' => 'User not found',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->serveNotFoundResponse('User not found', 'User controller');

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND, json_encode($expectedResponse));
    }

    public function testServeServerErrorResponse()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'ServerError',
                'message' => 'Oops ! Something went wrong.',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->serveServerErrorResponse('Oops ! Something went wrong.', 'User controller');

        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR, json_encode($expectedResponse));
    }

    public function testServeConflictResponse()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'Conflict',
                'message' => 'User already exists with this email',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->serveConflictResponse('User already exists with this email', 'User controller');

        $this->assertJsonResponse($response, Response::HTTP_CONFLICT, json_encode($expectedResponse));
    }

    public function testServeUnauthorizedResponse()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'Unauthorized',
                'message' => 'Bad credentials',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->serveUnauthorizedResponse('Bad credentials', 'User controller');

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED, json_encode($expectedResponse));
    }

    public function testServeAccessDeniedResponse()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'AccessDenied',
                'message' => 'Not allowed',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->serveAccessDeniedResponse('Not allowed', 'User controller');

        $this->assertJsonResponse($response, Response::HTTP_FORBIDDEN, json_encode($expectedResponse));
    }

    public function testServeBadRequestResponse()
    {
        $expectedResponse = [
            'error' => [
                'code' => 'BadRequest',
                'message' => 'Bad Request',
                'target' => 'User controller',
            ],
        ];

        $response = $this->controller->serveBadRequestResponse('Bad Request', 'User controller');

        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST, json_encode($expectedResponse));
    }

    private function assertJsonResponse(JsonResponse $response, string $expectedStatusCode, string $expectedJsonResponse): void
    {
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertEquals($expectedJsonResponse, $response->getContent());
    }
}

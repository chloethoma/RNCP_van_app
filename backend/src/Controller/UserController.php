<?php

namespace App\Controller;

use App\DTO\User\UserDTO;
use App\Handler\UserHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends ApiController
{
    public const TARGET = 'User controller';
    private const USER_NOT_FOUND_ERROR_MESSAGE = 'User not found';
    private const ACCESS_DENIED_ERROR_MESSAGE = 'You are not authorized to perform this action';

    public function __construct(
        LoggerInterface $logger,
        protected UserHandler $handler,
    ) {
        parent::__construct($logger);
    }

    #[Route(
        path: '/register',
        name: 'create_user',
        methods: ['POST'],
        format: 'json')]
    public function createUser(
        #[MapRequestPayload(validationGroups: ['create'], serializationContext: ['groups' => ['create']])] UserDTO $dto,
    ): JsonResponse {
        try {
            $newUser = $this->handler->handleCreate($dto);

            $response = $this->serveCreatedResponse($newUser, self::TARGET, groups: ['read']);
        } catch (ConflictHttpException $e) {
            $response = $this->serveConflictResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/user',
        name: 'read_user',
        methods: ['GET'],
        format: 'json')]
    public function getUserIdentity(): JsonResponse
    {
        try {
            $user = $this->handler->handleGet();

            $response = $this->serveOkResponse($user, groups: ['read']);
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::USER_NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (AccessDeniedHttpException $e) {
            $response = $this->serveAccessDeniedResponse(self::ACCESS_DENIED_ERROR_MESSAGE, self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/user/{userId}',
        name: 'edit_user',
        methods: ['PUT'],
        format: 'json')]
    public function updateUser(
        #[MapRequestPayload(validationGroups: ['update'], serializationContext: ['groups' => ['update']])] UserDTO $dto,
        int $userId,
    ): JsonResponse {
        try {
            $user = $this->handler->handleUpdate($userId, $dto);

            $response = $this->serveOkResponse($user, groups: ['read']);
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::USER_NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (AccessDeniedHttpException $e) {
            $response = $this->serveAccessDeniedResponse(self::ACCESS_DENIED_ERROR_MESSAGE, self::TARGET);
        } catch (ConflictHttpException $e) {
            $response = $this->serveConflictResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/user/{userId}',
        name: 'delete_user',
        methods: ['DELETE'],
        format: 'json')]
    public function deleteUser(int $userId): JsonResponse
    {
        try {
            $this->handler->handleDelete($userId);

            $response = $this->serveNoContentResponse();
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::USER_NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (AccessDeniedHttpException $e) {
            $response = $this->serveAccessDeniedResponse(self::ACCESS_DENIED_ERROR_MESSAGE, self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

<?php

namespace App\Controller;

use App\DTO\User\UserDTO;
use App\DTO\User\UserPasswordDTO;
use App\Handler\UserHandler;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserAccessDeniedException;
use App\Services\Exceptions\User\UserConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends ApiController
{
    public const TARGET = 'User controller';

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
        format: 'json'
    )]
    public function createUser(
        #[MapRequestPayload(validationGroups: ['create'], serializationContext: ['groups' => ['create']])] UserDTO $dto,
    ): JsonResponse {
        try {
            $newUser = $this->handler->handleCreate($dto);

            $response = $this->serveCreatedResponse($newUser, self::TARGET, groups: ['read']);
        } catch (UserConflictException $e) {
            $response = $this->serveConflictResponse($e->getMessage(), self::TARGET, $e->getDetails());
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/users',
        name: 'read_user',
        methods: ['GET'],
        format: 'json'
    )]
    public function getUserIdentity(): JsonResponse
    {
        try {
            $user = $this->handler->handleGet();

            $response = $this->serveOkResponse($user, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/users',
        name: 'edit_user',
        methods: ['PUT'],
        format: 'json'
    )]
    public function updateUser(
        #[MapRequestPayload(validationGroups: ['update'], serializationContext: ['groups' => ['update']])] UserDTO $dto,
    ): JsonResponse {
        try {
            $user = $this->handler->handleUpdate($dto);

            $response = $this->serveOkResponse($user, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (UserConflictException $e) {
            $response = $this->serveConflictResponse($e->getMessage(), self::TARGET, $e->getDetails());
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/users',
        name: 'edit_user_password',
        methods: ['PATCH'],
        format: 'json'
    )]
    public function updateUserPassword(
        #[MapRequestPayload(validationGroups: ['update'], serializationContext: ['groups' => ['update']])] UserPasswordDTO $dto,
    ): JsonResponse {
        try {
            $this->handler->handleUpdatePassword($dto);

            $response = $this->serveNoContentResponse();
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (UserAccessDeniedException $e) {
            $response = $this->serveAccessDeniedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/users',
        name: 'delete_user',
        methods: ['DELETE'],
        format: 'json'
    )]
    public function deleteUser(): JsonResponse
    {
        try {
            $this->handler->handleDelete();

            $response = $this->serveNoContentResponse();
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/users/summary',
        name: 'user_extra_infos',
        methods: ['GET'],
        format: 'json'
    )]
    public function getUserSummary(): JsonResponse
    {
        try {
            $userSummary = $this->handler->handleGetUserSummary();

            $response = $this->serveOkResponse($userSummary, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

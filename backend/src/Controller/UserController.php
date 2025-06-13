<?php

namespace App\Controller;

use App\DTO\User\UserDTO;
use App\DTO\User\UserPasswordDTO;
use App\DTO\User\UserSummaryDTO;
use App\Enum\ErrorMessage;
use App\Handler\UserHandler;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserAccessDeniedException;
use App\Services\Exceptions\User\UserConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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

    /**
     * Create new user.
     */
    #[Route(
        path: '/register',
        name: 'create_user',
        methods: ['POST'],
        format: 'json'
    )]
    #[OA\Tag(name: 'User')]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Returns data for the created user',
        content: new Model(type: UserDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_BAD_REQUEST,
        description: ErrorMessage::BAD_REQUEST->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CONFLICT,
        description: ErrorMessage::USER_CONFLICT->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Retrieves the authenticated user informations.
     */
    #[Route(
        path: '/api/users',
        name: 'read_user',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'User')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns data for a user',
        content: new Model(type: UserDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::USER_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Update authenticated user informations.
     */
    #[Route(
        path: '/api/users',
        name: 'edit_user',
        methods: ['PUT'],
        format: 'json'
    )]
    #[OA\Tag(name: 'User')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns updated data for the authenticated user',
        content: new Model(type: UserDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_BAD_REQUEST,
        description: ErrorMessage::BAD_REQUEST->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::USER_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CONFLICT,
        description: ErrorMessage::USER_CONFLICT->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Update authenticated user password.
     */
    #[Route(
        path: '/api/users',
        name: 'edit_user_password',
        methods: ['PATCH'],
        format: 'json'
    )]
    #[OA\Tag(name: 'User')]
    #[OA\Response(
        response: JsonResponse::HTTP_NO_CONTENT,
        description: 'Successfully updated the password',
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_BAD_REQUEST,
        description: ErrorMessage::BAD_REQUEST->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::USER_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_FORBIDDEN,
        description: ErrorMessage::USER_ACCESS_DENIED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Deletes the authenticated user account.
     */
    #[Route(
        path: '/api/users',
        name: 'delete_user',
        methods: ['DELETE'],
        format: 'json'
    )]
    #[OA\Tag(name: 'User')]
    #[OA\Response(
        response: JsonResponse::HTTP_NO_CONTENT,
        description: 'Successfully deleted the user',
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::USER_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Retrieves user extra infos summary (number of spots and friends).
     */
    #[Route(
        path: '/api/users/summary',
        name: 'user_extra_infos',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'User')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns extra informations data for the authenticated user',
        content: new Model(type: UserSummaryDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::USER_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

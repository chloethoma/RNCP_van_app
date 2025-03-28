<?php

namespace App\Controller;

use App\DTO\Friendship\FriendshipDTO;
use App\Handler\FriendshipHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class FriendshipController extends ApiController
{
    private const TARGET = 'Friendship controller';
    private const USER_NOT_FOUND_ERROR_MESSAGE = 'Friend not found';
    private const FRIENDSHIP_NOT_FOUND_ERROR_MESSAGE = 'Friendship not found';
    private const CONFLICT_ERROR_MESSAGE = 'Friendship already exists';
    private const BAD_REQUEST_ERROR_MESSAGE = 'The receiver id cannot be the same as the requester id';

    public function __construct(
        LoggerInterface $logger,
        protected FriendshipHandler $handler,
    ) {
        parent::__construct($logger);
    }

    #[Route(
        path: '/api/friendships',
        name: 'create_friendship',
        methods: ['POST'],
        format: 'json'
    )]
    public function createFriendship(
        #[MapRequestPayload(validationGroups: ['create'], serializationContext: ['groups' => ['create']])] FriendshipDTO $dto,
    ): JsonResponse {
        try {
            $newFriendship = $this->handler->handleCreate($dto);

            $response = $this->serveCreatedResponse($newFriendship, self::TARGET, groups: ['read']);
        } catch (BadRequestHttpException $e) {
            $response = $this->serveBadRequestResponse(self::BAD_REQUEST_ERROR_MESSAGE, self::TARGET);
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::USER_NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (ConflictHttpException $e) {
            $response = $this->serveConflictResponse(self::CONFLICT_ERROR_MESSAGE, self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/friendships/pending/{type}',
        name: 'read_pending_friendships',
        methods: ['GET'],
        requirements: ['type' => 'received|sent'],
        format: 'json'
    )]
    public function getFriendshipsRequestsSent(string $type): JsonResponse
    {
        try {
            $pendingFriendshipList = $this->handler->handleGetPendingFriendships($type);

            $response = $this->serveOkResponse($pendingFriendshipList, groups: ['read']);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/friendships/confirmed',
        name: 'read_confirmed_friendships',
        methods: ['GET'],
        format: 'json'
    )]
    public function getConfirmedFriendships(): JsonResponse
    {
        try {
            $friendshipList = $this->handler->handleGetConfirmFriendshipList();

            $response = $this->serveOkResponse($friendshipList, groups: ['read']);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/friendships/{requesterId}/confirm',
        name: 'update_confirm_friendship',
        requirements: ['requesterId' => Requirement::DIGITS],
        methods: ['PATCH'],
        format: 'json'
    )]
    public function updateConfirmedFriendship(int $requesterId): JsonResponse
    {
        try {
            $confirmFriendship = $this->handler->handleConfirmFriendship($requesterId);

            $response = $this->serveOkResponse($confirmFriendship, groups: ['read']);
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::FRIENDSHIP_NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/friendships/{friendId}/delete',
        name: 'delete_friendship',
        requirements: ['friendId' => Requirement::DIGITS],
        methods: ['DELETE'],
        format: 'json'
    )]
    public function deleteFriendship(int $friendId): JsonResponse
    {
        try {
            $this->handler->handleDeleteFriendship($friendId);

            $response = $this->serveNoContentResponse();
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::FRIENDSHIP_NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

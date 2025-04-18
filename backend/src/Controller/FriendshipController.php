<?php

namespace App\Controller;

use App\Handler\FriendshipHandler;
use App\Services\Exceptions\Friendship\FriendshipBadRequestException;
use App\Services\Exceptions\Friendship\FriendshipConflictException;
use App\Services\Exceptions\Friendship\FriendshipNotFoundException;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class FriendshipController extends ApiController
{
    private const TARGET = 'Friendship controller';

    public function __construct(
        LoggerInterface $logger,
        protected FriendshipHandler $handler,
    ) {
        parent::__construct($logger);
    }

    #[Route(
        path: '/api/friendships/{friendId}',
        name: 'create_friendship',
        methods: ['POST'],
        requirements: ['friendId' => Requirement::DIGITS],
        format: 'json'
    )]
    public function createFriendship(int $friendId): JsonResponse
    {
        try {
            $newFriendship = $this->handler->handleCreate($friendId);

            $response = $this->serveCreatedResponse($newFriendship, self::TARGET, groups: ['read']);
        } catch (FriendshipBadRequestException $e) {
            $response = $this->serveBadRequestResponse($e->getMessage(), self::TARGET);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (FriendshipConflictException $e) {
            $response = $this->serveConflictResponse($e->getMessage(), self::TARGET);
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
    public function getPendingFriendshipList(string $type): JsonResponse
    {
        try {
            $pendingFriendshipList = $this->handler->handleGetPendingFriendships($type);

            $response = $this->serveOkResponse($pendingFriendshipList, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/friendships/pending/received/summary',
        name: 'read_pending_friendships_summary',
        methods: ['GET'],
        format: 'json'
    )]
    public function getReceivedFriendshipSummary(): JsonResponse
    {
        try {
            $summary = $this->handler->handleGetReceivedFriendshipSummary();

            $response = $this->serveOkResponse($summary, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
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
    public function getConfirmedFriendshipList(): JsonResponse
    {
        try {
            $friendshipList = $this->handler->handleGetConfirmFriendshipList();

            $response = $this->serveOkResponse($friendshipList, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
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
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (FriendshipNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: '/api/friendships/{friendId}',
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
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (FriendshipNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

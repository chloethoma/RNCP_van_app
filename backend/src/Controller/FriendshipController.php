<?php

namespace App\Controller;

use App\DTO\Friendship\FriendshipDTO;
use App\DTO\Friendship\FriendshipReceivedSummaryDTO;
use App\DTO\Friendship\PartialFriendshipDTO;
use App\Enum\ErrorMessage;
use App\Handler\FriendshipHandler;
use App\Services\Exceptions\Friendship\FriendshipBadRequestException;
use App\Services\Exceptions\Friendship\FriendshipConflictException;
use App\Services\Exceptions\Friendship\FriendshipNotFoundException;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserNotFoundException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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

    /**
     * Creates a new friendship between the authenticated user and the user with the given friendId.
     *
     * @param int $friendId the ID of the user to send a friendship request to
     */
    #[Route(
        path: '/api/friendships/{friendId}',
        name: 'create_friendship',
        methods: ['POST'],
        requirements: ['friendId' => Requirement::DIGITS],
        format: 'json'
    )]
    #[OA\Tag(name: 'Friendship')]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Returns data for the created friendship',
        content: new Model(type: FriendshipDTO::class, groups: ['read'])
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
        description: ErrorMessage::FRIENDSHIP_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CONFLICT,
        description: ErrorMessage::FRIENDSHIP_CONFLICT->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Retrieves a list of pending friendship requests (received or sent) for the authenticated user.
     *
     * @param string $type the type of pending requests to retrieve ("received" or "sent")
     */
    #[Route(
        path: '/api/friendships/pending/{type}',
        name: 'read_pending_friendships',
        methods: ['GET'],
        requirements: ['type' => 'received|sent'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Friendship')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns a list of pending friendship requests (received or sent)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: PartialFriendshipDTO::class, groups: ['read']))
        )
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Retrieves the number of pending friendship requests received by the authenticated user.
     */
    #[Route(
        path: '/api/friendships/pending/received/summary',
        name: 'read_pending_friendships_summary',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Friendship')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns the number of pending friendship requests received',
        content: new Model(type: FriendshipReceivedSummaryDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Retrieves the list of confirmed friendships for the authenticated user.
     */
    #[Route(
        path: '/api/friendships/confirmed',
        name: 'read_confirmed_friendships',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Friendship')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns a list of confirmed friendship',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: PartialFriendshipDTO::class, groups: ['read']))
        )
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Confirms a friendship request by setting its `isConfirmed` status to true.
     *
     * @param int $requesterId the ID of the user who originally sent the friendship request
     */
    #[Route(
        path: '/api/friendships/{requesterId}/confirm',
        name: 'update_confirm_friendship',
        requirements: ['requesterId' => Requirement::DIGITS],
        methods: ['PATCH'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Friendship')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Return data for the updated friendship',
        content: new Model(type: FriendshipDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::FRIENDSHIP_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

    /**
     * Deletes a friendship between the authenticated user and the specified friend.
     *
     * @param int $friendId the ID of the friend to remove from the authenticated user's friendship list
     */
    #[Route(
        path: '/api/friendships/{friendId}',
        name: 'delete_friendship',
        requirements: ['friendId' => Requirement::DIGITS],
        methods: ['DELETE'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Friendship')]
    #[OA\Response(
        response: JsonResponse::HTTP_NO_CONTENT,
        description: 'Successfully deleted the friendship',
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::FRIENDSHIP_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
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

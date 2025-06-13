<?php

namespace App\Controller;

use App\DTO\Spot\SpotDTO;
use App\DTO\SpotGeoJson\SpotCollectionDTO;
use App\Enum\ErrorMessage;
use App\Handler\SpotHandler;
use App\Services\Exceptions\Spot\SpotAccessDeniedException;
use App\Services\Exceptions\Spot\SpotNotFoundException;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserNotFoundException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;

class SpotController extends ApiController
{
    private const TARGET = 'Spot Controller';

    public function __construct(
        LoggerInterface $logger,
        protected SpotHandler $handler,
    ) {
        parent::__construct($logger);
    }

    // =====================================
    // 📌 SPOT ROUTES
    // =====================================

    /**
     * Create new spot.
     */
    #[Route(
        path: 'api/spots',
        name: 'create_spot',
        methods: ['POST']
    )]
    #[OA\Tag(name: 'Spot')]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Returns data for the created spot',
        content: new Model(type: SpotDTO::class, groups: ['read'])
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
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
    )]
    public function createSpot(
        #[MapRequestPayload(validationGroups: ['create'], serializationContext: ['groups' => 'create'])] SpotDTO $dto,
        UrlGeneratorInterface $urlGenerator,
    ): JsonResponse {
        try {
            $newSpot = $this->handler->handleCreate($dto);

            $response = $this->serveCreatedResponse(
                $newSpot,
                $urlGenerator->generate('read_spot', ['spotId' => $newSpot->id]),
                groups: ['read']
            );
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
     * Retrieves the list of spots for the authenticated user.
     */
    #[Route(
        path: 'api/spots',
        name: 'read_all_spots',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Spot')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns a list of spots',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: SpotCollectionDTO::class))
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
    public function getSpots(): JsonResponse
    {
        try {
            $spotList = $this->handler->handleGetSpotCollection();

            $response = $this->serveOkResponse($spotList);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    /**
     * Retrieves a spot informations.
     *
     * @param int $spotId The ID of the spot to read
     */
    #[Route(
        path: 'api/spots/{spotId}',
        name: 'read_spot',
        methods: ['GET'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
    )]
    #[OA\Tag(name: 'Spot')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Return data for a spot',
        content: new Model(type: SpotDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::SPOT_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_FORBIDDEN,
        description: ErrorMessage::SPOT_ACCESS_DENIED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
    )]
    public function getSpot(int $spotId): JsonResponse
    {
        try {
            $spot = $this->handler->handleGet($spotId);

            $response = $this->serveOkResponse($spot, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (SpotNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (SpotAccessDeniedException $e) {
            $response = $this->serveAccessDeniedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    /**
     * Update spot informations of the given spotId.
     *
     * @param int $spotId The ID of the spot to update
     */
    #[Route(
        path: 'api/spots/{spotId}',
        name: 'edit_spot',
        methods: ['PUT'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
    )]
    #[OA\Tag(name: 'Spot')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Return data for the updated spot',
        content: new Model(type: SpotDTO::class, groups: ['read'])
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
        description: ErrorMessage::SPOT_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_FORBIDDEN,
        description: ErrorMessage::SPOT_ACCESS_DENIED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
    )]
    public function updateSpot(
        #[MapRequestPayload(validationGroups: ['update'], serializationContext: ['groups' => 'update'])] SpotDTO $dto,
        int $spotId,
    ): JsonResponse {
        try {
            $spot = $this->handler->handleUpdate($dto, $spotId);

            $response = $this->serveOkResponse($spot, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (SpotNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (SpotAccessDeniedException $e) {
            $response = $this->serveAccessDeniedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    /**
     * Deletes a spot.
     *
     * @param int $spotId The ID of the spot to remove
     */
    #[Route(
        path: 'api/spots/{spotId}',
        name: 'delete_spot',
        methods: ['DELETE'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
    )]
    #[OA\Tag(name: 'Spot')]
    #[OA\Response(
        response: JsonResponse::HTTP_NO_CONTENT,
        description: 'Successfully deleted the spot',
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::SPOT_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_FORBIDDEN,
        description: ErrorMessage::SPOT_ACCESS_DENIED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
    )]
    public function deleteSpot(int $spotId): JsonResponse
    {
        try {
            $this->handler->handleDelete($spotId);

            $response = $this->serveNoContentResponse();
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (SpotNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (SpotAccessDeniedException $e) {
            $response = $this->serveAccessDeniedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    // =====================================
    // 📌 SPOT OF FRIENDS
    // =====================================

    /**
     * Retrieves the list of spots shared by the friends of the authenticated user.
     */
    #[Route(
        path: 'api/spots/friends',
        name: 'read_all_spots_friends',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Spot of friends')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Returns a list of spots shared by the friends of the authenticated user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: SpotCollectionDTO::class))
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
    public function getFriendsSpots(): JsonResponse
    {
        try {
            $spotList = $this->handler->handleGetSpotFriendsCollection();

            $response = $this->serveOkResponse($spotList);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    /**
     * Retrieves a spot informations share by a friend.
     *
     * @param int $spotId The ID of the spot to read
     */
    #[Route(
        path: 'api/spots/{spotId}/friends',
        name: 'read_spot_friend',
        methods: ['GET'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
    )]
    #[OA\Tag(name: 'Spot of friends')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Return data for a spot share by a friend',
        content: new Model(type: SpotDTO::class, groups: ['read'])
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::SPOT_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_FORBIDDEN,
        description: ErrorMessage::SPOT_ACCESS_DENIED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
    )]
    public function getFriendSpot(int $spotId): JsonResponse
    {
        try {
            $spot = $this->handler->handleGetSpotFriend($spotId);

            $response = $this->serveOkResponse($spot, groups: ['read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (SpotNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (SpotAccessDeniedException $e) {
            $response = $this->serveAccessDeniedResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

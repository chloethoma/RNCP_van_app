<?php

namespace App\Controller;

use App\DTO\Spot\SpotDTO;
use App\Handler\SpotHandler;
use App\Services\Exceptions\Spot\SpotAccessDeniedException;
use App\Services\Exceptions\Spot\SpotNotFoundException;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserNotFoundException;
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

    #[Route(
        path: 'api/spots',
        name: 'create_spot',
        methods: ['POST']
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

    #[Route(
        path: 'api/spots',
        name: 'read_all_spots',
        methods: ['GET'],
        format: 'json'
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

    #[Route(
        path: 'api/spots/{spotId}',
        name: 'read_spot',
        methods: ['GET'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
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

    #[Route(
        path: 'api/spots/{spotId}',
        name: 'edit_spot',
        methods: ['PUT'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
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

    #[Route(
        path: 'api/spots/{spotId}',
        name: 'delete_spot',
        methods: ['DELETE'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
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

    #[Route(
        path: 'api/spots/friends',
        name: 'read_all_spots_friends',
        methods: ['GET'],
        format: 'json'
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

    #[Route(
        path: 'api/spots/{spotId}/friends',
        name: 'read_spot_friend',
        methods: ['GET'],
        requirements: ['spotId' => Requirement::DIGITS],
        format: 'json'
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

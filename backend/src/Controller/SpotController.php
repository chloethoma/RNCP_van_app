<?php

namespace App\Controller;

use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Spot\SpotDTO;
use App\Handler\SpotHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;

class SpotController extends ApiController
{
    private const TARGET = 'Spot Controller';
    private const NOT_FOUND_ERROR_MESSAGE = 'User not found';

    public function __construct(
        LoggerInterface $logger,
        protected SpotHandler $spotHandler,
    ) {
        parent::__construct($logger);
    }

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
            $newSpot = $this->spotHandler->handleCreate($dto);

            $response = $this->serveCreatedResponse(
                $newSpot,
                $urlGenerator->generate('read_spot', ['spotId' => $newSpot->id]),
                groups: ['read']
            );
        } catch (NotFoundHttpException $e) {
            $response = $this->serveNotFoundResponse(self::NOT_FOUND_ERROR_MESSAGE, self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }

    #[Route(
        path: 'api/spots',
        name: 'read_spots',
        methods: ['GET'],
        format: 'json'
    )]
    public function getSpots(): JsonResponse
    {
        try {
            $spotCollection = $this->spotHandler->handleGetFeatureCollection();

            $response = $this->serveOkResponse($spotCollection);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, SpotFeatureCollectionDTO::class);
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
            $spot = $this->spotHandler->handleGet($spotId);

            $response = $this->serveOkResponse($spot, groups: ['read']);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, SpotFeatureCollectionDTO::class);
        }

        return $response;
    }
}

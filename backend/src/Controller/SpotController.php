<?php

namespace App\Controller;

use App\DTO\Feature\SpotFeatureCollection;
use App\Handler\SpotHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SpotController extends ApiController
{
    public function __construct(
        LoggerInterface $logger,
        protected SpotHandler $spotHandler,
    ) {
        parent::__construct($logger);
    }

    #[Route('api/spots', name: 'read_spots', methods: ['GET'], format: 'json')]
    public function readSpotFeatureCollection(): JsonResponse
    {
        try {
            $spotCollection = $this->spotHandler->getSpotsFeatureCollection();

            $response = $this->serveOkResponse($spotCollection);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, SpotFeatureCollection::class);
        }

        return $response;
    }

    // #[Route('/spots', name: 'create_spot', methods: ['POST'])]
    // public function createTest(Request $request): JsonResponse
    // {
    //     $param = json_decode($request->getContent(), true);

    //     $test = new Spot();
    //     $test->setLatitude($param['latitude']);
    //     $test->setLongitude($param['longitude']);
    //     $test->setDescription($param['description']);

    //     $this->em->persist($test);
    //     $this->em->flush();

    //     return $this->json([
    //         "message" => sprintf('New entry save ! id n°%d', $test->getId()),
    //         "id" => $test->getId()
    //     ]);
    // }
}

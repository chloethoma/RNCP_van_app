<?php

namespace App\Controller;

use App\DTO\Feature\SpotFeatureCollectionOutput;
use App\Service\Manager\SpotManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SpotController extends ApiController
{
    public function __construct(
        LoggerInterface $logger,
        protected SpotManager $manager,
    ) {
        parent::__construct($logger);
    }

    #[Route('/spots', name: 'read_spots', methods: ['GET'], format: 'json')]
    public function readSpotFeatureCollection(): JsonResponse
    {
        try {
            $spotCollection = $this->manager->getSpotsFeatureCollection();
            dump('coucou');
            $response = $this->serveOkResponse($spotCollection);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, SpotFeatureCollectionOutput::class);
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

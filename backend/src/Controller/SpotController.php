<?php

namespace App\Controller;

use App\Service\Manager\SpotManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpotController extends AbstractController
{
    public function __construct(
        protected SpotManager $manager
    ) {}

    #[Route('/spots', name: 'read_spots', methods: ['GET'], format: 'json')]
    public function readSpotFeatureCollection(): Response
    {
        $spotCollection = $this->manager->getSpotsFeatureCollection();

        return $this->json($spotCollection);
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

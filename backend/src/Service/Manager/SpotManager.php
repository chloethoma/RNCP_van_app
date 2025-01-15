<?php

namespace App\Service\Manager;

use App\DTO\Feature\SpotFeatureCollectionOutput;
use App\Entity\Spot;
use App\Repository\SpotRepository;
use App\Service\DataTransformer\SpotDataTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SpotManager
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected SpotRepository $repository,
        protected SpotDataTransformer $transformer,
        protected EntityManagerInterface $em,
    ) {
    }

    public function getSpotsFeatureCollection(): SpotFeatureCollectionOutput
    {
        $spotEntities = $this->em->getRepository(Spot::class)->findAll();

        return $this->transformer->transformToFeatureCollection($spotEntities);
    }
}

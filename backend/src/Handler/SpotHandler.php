<?php

namespace App\Handler;

use App\DTO\Feature\SpotFeatureCollection;
use App\Entity\Spot;
use App\Repository\SpotRepository;
use App\Service\DataTransformer\SpotDataTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SpotHandler
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected SpotRepository $repository,
        protected SpotDataTransformer $transformer,
        protected EntityManagerInterface $em,
    ) {
    }

    public function getSpotsFeatureCollection(): SpotFeatureCollection
    {
        $spotEntities = $this->em->getRepository(Spot::class)->findAll();

        return $this->transformer->transformToFeatureCollection($spotEntities);
    }
}

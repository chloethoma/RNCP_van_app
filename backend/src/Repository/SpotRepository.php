<?php

namespace App\Repository;

use App\DataTransformer\FeatureDataTransformer;
use App\Entity\Spot;
use App\Entity\SpotCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Spot>
 */
class SpotRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private FeatureDataTransformer $transformer,
    ) {
        parent::__construct($registry, Spot::class);
    }

    public function createSpot(Spot $spot): Spot
    {
        $this->getEntityManager()->persist($spot);
        $this->getEntityManager()->flush();

        return $spot;
    }

    public function getSpotCollection(int $userId): SpotCollection
    {
        $spotList = $this->findBy(['owner' => $userId]);

        return $this->transformer->transformArrayInObjectList($spotList);
    }

    public function getSpotById(int $spotId): ?Spot
    {
        return $this->findOneBy(['id' => $spotId]);
    }
}

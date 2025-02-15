<?php

namespace App\Repository;

use App\DataTransformer\FeatureDataTransformer;
use App\Entity\Spot;
use App\Entity\SpotCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function create(Spot $spot): Spot
    {
        $this->getEntityManager()->persist($spot);
        $this->getEntityManager()->flush();

        return $spot;
    }

    public function update(Spot $spot): Spot
    {
        $this->getEntityManager()->flush();

        return $spot;
    }

    public function delete(Spot $spot): void
    {
        $this->getEntityManager()->remove($spot);
        $this->getEntityManager()->flush();
    }

    public function findCollection(int $userId): SpotCollection
    {
        $spotList = $this->findBy(['owner' => $userId]);
        
        if (!$spotList) {
            throw new NotFoundHttpException();
        }

        return $this->transformer->transformArrayInObjectList($spotList);
    }

    public function findById(int $spotId): ?Spot
    {
        $spot = $this->findOneBy(['id' => $spotId]);

        if (!$spot) {
            throw new NotFoundHttpException();
        }

        return $spot;
    }
}

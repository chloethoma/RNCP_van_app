<?php

namespace App\Repository;

use App\Entity\Friendship;
use App\Entity\Spot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Spot>
 */
class SpotRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Spot::class);
    }

    public function findCollection(int $userId): array
    {
        return $this->findBy(['owner' => $userId]);
    }

    public function findById(int $spotId): ?Spot
    {
        return $this->find($spotId);
    }

    public function findFriendsSpots(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin(
                Friendship::class,
                'f',
                'WITH',
                '(f.requester = s.owner AND f.receiver = :user) OR (f.receiver = s.owner AND f.requester = :user)'
            )
            ->where('f.isConfirmed = true')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }
}

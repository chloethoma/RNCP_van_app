<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function countFriendships(int $requesterId, int $receiverId): int
    {
        return (int) $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('(f.requester = :requester AND f.receiver = :receiver)')
            ->orWhere('(f.requester = :receiver AND f.receiver = :requester)')
            ->setParameter('requester', $requesterId)
            ->setParameter('receiver', $receiverId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

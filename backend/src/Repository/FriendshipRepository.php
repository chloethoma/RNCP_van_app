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

    public function friendshipExists(int $requesterId, int $receiverId): bool
    {
        return (bool) $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('(f.requester = :requester AND f.receiver = :receiver)')
            ->orWhere('(f.requester = :receiver AND f.receiver = :requester)')
            ->setParameter('requester', $requesterId)
            ->setParameter('receiver', $receiverId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPendingFriendshipsByUserIdAndType(int $userId, string $type): array
    {
        $field = 'received' === $type ? 'f.receiver' : 'f.requester';

        return $this->createQueryBuilder('f')
            ->addSelect('PARTIAL requester.{id, pseudo, picture}', 'PARTIAL receiver.{id, pseudo, picture}')
            ->leftJoin('f.requester', 'requester')
            ->leftJoin('f.receiver', 'receiver')
            ->where("$field = :userId")
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}

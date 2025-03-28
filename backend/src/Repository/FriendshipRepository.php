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

    public function isfriendshipExist(int $requesterId, int $receiverId): bool
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
            ->andWhere('f.isConfirmed = false')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findConfirmFriendships(int $userId): array
    {
        return $this->createQueryBuilder('f')
            ->addSelect('PARTIAL requester.{id, pseudo, picture}', 'PARTIAL receiver.{id, pseudo, picture}')
            ->leftJoin('f.requester', 'requester')
            ->leftJoin('f.receiver', 'receiver')
            ->where('(f.requester = :user OR f.receiver = :user)')
            ->andWhere('f.isConfirmed = true')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findOneFriendshipById(int $userId, int $friendId): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->addSelect('PARTIAL requester.{id, pseudo, picture}', 'PARTIAL receiver.{id, pseudo, picture}')
            ->leftJoin('f.requester', 'requester')
            ->leftJoin('f.receiver', 'receiver')
            ->where('(f.requester = :user AND f.receiver = :friend)')
            ->orWhere('(f.requester = :friend AND f.receiver = :user)')
            ->setParameter('user', $userId)
            ->setParameter('friend', $friendId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

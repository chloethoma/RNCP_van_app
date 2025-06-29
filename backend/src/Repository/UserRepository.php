<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Get User by his token identifier (eg. email).
     */
    public function findByUserIdentifier(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Get User by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Get User by pseudo.
     */
    public function findByPseudo(string $pseudo): ?User
    {
        return $this->findOneBy(['pseudo' => $pseudo]);
    }

    /**
     * Get list of user which are not already friends with the authenticated one.
     *
     * @return User[]
     */
    public function searchUsersNotFriendsWithCurrentUser(string $pseudo, int $currentUserId): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin(
                'App\Entity\Friendship',
                'f',
                'WITH',
                '(f.requester = :currentUserId AND f.receiver = u.id) 
                OR (f.requester = u.id AND f.receiver = :currentUserId)'
            )
            ->where('u.pseudo LIKE :pseudo')
            ->andWhere('u.id != :currentUserId')
            ->andWhere('f.id IS NULL')
            ->setParameter('pseudo', $pseudo.'%')
            ->setParameter('currentUserId', $currentUserId)
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }
}

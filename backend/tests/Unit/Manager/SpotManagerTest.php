<?php

namespace App\Tests\Manager;

use App\Entity\Spot;
use App\Entity\User;
use App\Manager\SpotManager;
use App\Manager\UserManager;
use App\Repository\FriendshipRepository;
use App\Services\Exceptions\Spot\SpotAccessDeniedException;
use App\Services\Exceptions\User\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class SpotManagerTest extends TestCase
{
    private const AUTH_USER_ID = 42;
    private const OWNER_ID = 2;
    private const FRIEND_ID = 1;
    private const WRONG_USER_ID = 99;
    private const SAME_USER_ID = 10;

    private SpotManager $spotManager;
    private UserManager $userManager;
    private FriendshipRepository $friendshipRepository;
    private EntityManagerInterface $em;
    private ObjectRepository $userRepository;

    protected function setUp(): void
    {
        $this->userManager = $this->createMock(UserManager::class);
        $this->friendshipRepository = $this->createMock(FriendshipRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(EntityRepository::class);

        $this->em
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->spotManager = new SpotManager(
            $this->em,
            $this->userManager,
            $this->friendshipRepository
        );
    }

    public function testInitSpotOwnerUserFound(): void
    {
        $user = $this->createUser(self::AUTH_USER_ID);
        $spot = new Spot();

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn(self::AUTH_USER_ID);

        $this->userRepository
            ->method('find')
            ->with(self::AUTH_USER_ID)
            ->willReturn($user);

        $result = $this->spotManager->initSpotOwner($spot);

        $this->assertSame($user, $result->getOwner());
    }

    public function testInitSpotOwnerUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $spot = new Spot();

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn(self::AUTH_USER_ID);

        $this->userRepository
            ->method('find')
            ->with(self::AUTH_USER_ID)
            ->willReturn(null);

        $this->spotManager->initSpotOwner($spot);
    }

    public function testCheckAccessWithCorrectUser(): void
    {
        $user = $this->createUser(self::SAME_USER_ID);
        $spot = (new Spot())->setOwner($user);

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn(self::SAME_USER_ID);

        $this->spotManager->checkAccess($spot);

        $this->assertTrue(true);
    }

    public function testCheckAccessWithIncorrectUser(): void
    {
        $this->expectException(SpotAccessDeniedException::class);

        $user = $this->createUser(self::SAME_USER_ID);
        $spot = (new Spot())->setOwner($user);

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn(self::WRONG_USER_ID);

        $this->spotManager->checkAccess($spot);
    }

    public function testCheckSpotFriendAccessWithFriendship(): void
    {
        $userId = self::FRIEND_ID;
        $spotOwnerId = self::OWNER_ID;
        $owner = $this->createUser($spotOwnerId);
        $spot = (new Spot())->setOwner($owner);

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn($userId);

        $this->friendshipRepository
            ->method('isFriendshipExist')
            ->with($spotOwnerId, $userId)
            ->willReturn(true);

        $this->spotManager->checkSpotFriendAccess($spot);

        $this->assertTrue(true);
    }

    public function testCheckSpotFriendAccessWithoutFriendship(): void
    {
        $this->expectException(SpotAccessDeniedException::class);

        $userId = self::FRIEND_ID;
        $spotOwnerId = self::OWNER_ID;
        $owner = $this->createUser($spotOwnerId);
        $spot = (new Spot())->setOwner($owner);

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn($userId);

        $this->friendshipRepository
            ->method('isFriendshipExist')
            ->with($spotOwnerId, $userId)
            ->willReturn(false);

        $this->spotManager->checkSpotFriendAccess($spot);
    }

    private function createUser(int $id): User
    {
        return (new User())->setId($id);
    }
}

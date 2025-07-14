<?php

namespace App\Tests\Manager;

use App\Entity\Friendship;
use App\Entity\User;
use App\Manager\FriendshipManager;
use App\Manager\UserManager;
use App\Repository\FriendshipRepository;
use App\Services\Exceptions\Friendship\FriendshipConflictException;
use App\Services\Exceptions\User\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class FriendshipManagerTest extends TestCase
{
    private const USER_ID_1 = 1;
    private const USER_ID_2 = 2;
    private const USER_ID_NOT_FOUND = 99;

    private EntityManagerInterface $em;
    private EntityRepository $userRepository;
    private UserManager $userManager;
    private FriendshipRepository $friendshipRepository;
    private FriendshipManager $friendshipManager;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(EntityRepository::class);
        $this->userManager = $this->createMock(UserManager::class);
        $this->friendshipRepository = $this->createMock(FriendshipRepository::class);

        $this->em->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->friendshipManager = new FriendshipManager(
            $this->em,
            $this->userManager,
            $this->friendshipRepository
        );
    }

    public function testInitNewFriendship(): void
    {
        $friendship = $this->friendshipManager->initNewFriendship();

        $this->assertFalse($friendship->isConfirmed());
    }

    public function testInitConfirmFriendship(): void
    {
        $friendship = new Friendship();
        $friendship->setConfirmed(false);

        $result = $this->friendshipManager->initConfirmFriendship($friendship);

        $this->assertTrue($result->isConfirmed());
    }

    public function testInitAuthenticatedUser(): void
    {
        $friendship = new Friendship();
        $user = $this->createUser(self::USER_ID_1);

        $this->userManager
            ->method('getAuthenticatedUserId')
            ->willReturn(self::USER_ID_1);

        $this->userRepository
            ->method('find')
            ->with(self::USER_ID_1)
            ->willReturn($user);

        $result = $this->friendshipManager->initAuthenticatedUser($friendship);

        $this->assertSame($user, $result->getRequester());
    }

    public function testInitFriendUser(): void
    {
        $friendship = new Friendship();
        $friendUser = $this->createUser(self::USER_ID_2);

        $this->userRepository
            ->method('find')
            ->with(self::USER_ID_2)
            ->willReturn($friendUser);

        $result = $this->friendshipManager->initFriendUser(self::USER_ID_2, $friendship);

        $this->assertSame($friendUser, $result->getReceiver());
    }

    public function testFindUserByIdThrowsUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository
            ->method('find')
            ->with(self::USER_ID_NOT_FOUND)
            ->willReturn(null);

        $this->friendshipManager->initFriendUser(self::USER_ID_NOT_FOUND, new Friendship());
    }

    public function testIsReceiverIdDifferentFromCurrentUserTrue(): void
    {
        $result = $this->friendshipManager->isReceiverIdDifferentFromCurrentUser(1, 2);
        $this->assertTrue($result);
    }

    public function testIsReceiverIdDifferentFromCurrentUserFalse(): void
    {
        $result = $this->friendshipManager->isReceiverIdDifferentFromCurrentUser(1, 1);
        $this->assertFalse($result);
    }

    public function testCheckIfFriendshipAlreadyExistsThrowsConflict(): void
    {
        $this->expectException(FriendshipConflictException::class);

        $requester = $this->createUser(self::USER_ID_1);
        $receiver = $this->createUser(self::USER_ID_2);

        $friendship = new Friendship();
        $friendship->setRequester($requester);
        $friendship->setReceiver($receiver);

        $this->friendshipRepository
            ->method('isFriendshipExist')
            ->with(self::USER_ID_1, self::USER_ID_2)
            ->willReturn(true);

        $this->friendshipManager->checkIfFriendshipAlreadyExists($friendship);
    }

    public function testCheckIfFriendshipAlreadyExistsNoConflict(): void
    {
        $requester = $this->createUser(self::USER_ID_1);
        $receiver = $this->createUser(self::USER_ID_2);

        $friendship = new Friendship();
        $friendship->setRequester($requester);
        $friendship->setReceiver($receiver);

        $this->friendshipRepository
            ->method('isFriendshipExist')
            ->with(self::USER_ID_1, self::USER_ID_2)
            ->willReturn(false);

        $this->friendshipManager->checkIfFriendshipAlreadyExists($friendship);

        $this->assertTrue(true);
    }

    private function createUser(int $id): User
    {
        $user = new User();
        $user->setId($id);

        return $user;
    }
}

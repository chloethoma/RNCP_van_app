<?php

namespace App\Tests\Integration\Handler;

use App\DTO\Spot\SpotDTO;
use App\DTO\SpotGeoJson\SpotCollectionDTO;
use App\Entity\Friendship;
use App\Entity\Spot;
use App\Entity\User;
use App\Handler\SpotHandler;
use App\Services\Exceptions\Spot\SpotAccessDeniedException;
use App\Services\Exceptions\Spot\SpotNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SpotHandlerTest extends KernelTestCase
{
    private const USER_EMAIL = 'user1@example.com';
    private const USER_PSEUDO = 'User1';
    private const OTHER_USER_EMAIL = 'other_user12@example.com';

    private const SPOT_LONGITUDE = 1.2345;
    private const SPOT_LATITUDE = 5.6789;
    private const SPOT_DESCRIPTION = 'test create spot';
    private const SPOT_IS_FAVORITE = false;
    private const SPOT_UPDATE_DESCRIPTION = 'test spot update';

    private SpotHandler $handler;
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->handler = $container->get(SpotHandler::class);
        $this->em = $container->get(EntityManagerInterface::class);
        $this->tokenStorage = $container->get(TokenStorageInterface::class);
    }

    // ----------------------------------------------------------------------------
    // Create Spot Tests
    // ----------------------------------------------------------------------------
    public function testHandleCreateSuccess(): void
    {
        $user = $this->authenticateTestUser(self::USER_EMAIL);

        $dto = $this->createSpotDTO(self::SPOT_LONGITUDE, self::SPOT_LATITUDE, self::SPOT_DESCRIPTION);

        $createdSpotDTO = $this->handler->handleCreate($dto);

        $this->assertEquals(self::USER_PSEUDO, $createdSpotDTO->owner->pseudo);
        $this->assertNotNull($createdSpotDTO->id);
        $this->assertEquals(self::SPOT_LONGITUDE, $createdSpotDTO->longitude);
        $this->assertEquals(self::SPOT_LATITUDE, $createdSpotDTO->latitude);
        $this->assertEquals(self::SPOT_DESCRIPTION, $createdSpotDTO->description);
        $this->assertequals(self::SPOT_IS_FAVORITE, $createdSpotDTO->isFavorite);
    }

    // ----------------------------------------------------------------------------
    // Get Spot By ID Tests
    // ----------------------------------------------------------------------------
    public function testHandleGetSuccess(): void
    {
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        $resultDTO = $this->handler->handleGet($createdSpot->id);

        $this->assertEquals($createdSpot->id, $resultDTO->id);
    }

    public function testHandleGetNotFoundException(): void
    {
        $this->authenticateTestUser(self::USER_EMAIL);

        $this->expectException(SpotNotFoundException::class);

        $this->handler->handleGet(999999);
    }

    public function testHandleGetAccessDeniedException(): void
    {
        // Create Spot for a user
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        // Change the authenticated user to force and simulate an accessDeniedException
        $this->authenticateTestUser(self::OTHER_USER_EMAIL);

        $this->expectException(SpotAccessDeniedException::class);

        $this->handler->handleGet($createdSpot->id);
    }

    // ----------------------------------------------------------------------------
    // Update Spot Tests
    // ----------------------------------------------------------------------------
    public function testHandleUpdateSuccess(): void
    {
        // Create a spot for a user to get an existing id in database, with an identified user
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        $spotDTO = new SpotDTO(
            longitude: $createdSpot->longitude,
            latitude: $createdSpot->latitude,
            description: self::SPOT_UPDATE_DESCRIPTION,
            owner: $createdSpot->owner,
            isFavorite: $createdSpot->isFavorite,
            id: $createdSpot->id
        );

        $updatedDTO = $this->handler->handleUpdate($spotDTO, $createdSpot->id);

        $this->assertNotEquals(self::SPOT_UPDATE_DESCRIPTION, $createdSpot->description);
        $this->assertEquals(self::SPOT_UPDATE_DESCRIPTION, $updatedDTO->description);
    }

    public function testHandleUpdateNotFoundException(): void
    {
        // Create a spot for a user to get an existing id in database, with an identified user
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        $spotDTO = new SpotDTO(
            longitude: $createdSpot->longitude,
            latitude: $createdSpot->latitude,
            description: self::SPOT_UPDATE_DESCRIPTION,
            owner: $createdSpot->owner,
            isFavorite: $createdSpot->isFavorite,
            id: $createdSpot->id
        );

        $this->expectException(SpotNotFoundException::class);

        $this->handler->handleUpdate($spotDTO, 999999);
    }

    public function testHandleUpdateAccessDeniedException(): void
    {
        // Create a spot for a user to get an existing id in database, with an identified user
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        // Change the authenticated user to force and simulate an accessDeniedException
        $this->authenticateTestUser(self::OTHER_USER_EMAIL);

        $spotDTO = new SpotDTO(
            longitude: $createdSpot->longitude,
            latitude: $createdSpot->latitude,
            description: self::SPOT_UPDATE_DESCRIPTION,
            owner: $createdSpot->owner,
            isFavorite: $createdSpot->isFavorite,
            id: $createdSpot->id
        );

        $this->expectException(SpotAccessDeniedException::class);

        $this->handler->handleUpdate($spotDTO, $createdSpot->id);
    }

    // ----------------------------------------------------------------------------
    // Delete Spot Tests
    // ----------------------------------------------------------------------------
    public function testHandleDeleteSuccess(): void
    {
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        $this->handler->handleDelete($createdSpot->id);

        $deletedSpot = $this->em->getRepository(Spot::class)->findOneBy(['id' => $createdSpot->id]);

        $this->assertEquals(null, $deletedSpot);
    }

    public function testHandleDeleteeNotFoundException(): void
    {
        $this->authenticateTestUser(self::USER_EMAIL);

        $this->expectException(SpotNotFoundException::class);

        $this->handler->handleDelete(999999);
    }

    public function testHandleDeleteAccessDeniedException(): void
    {
        // Create Spot for a user
        $createdSpot = $this->createSpotForUser(self::USER_EMAIL);

        // Change the authenticated user to force and simulate an accessDeniedException
        $this->authenticateTestUser(self::OTHER_USER_EMAIL);

        $this->expectException(SpotAccessDeniedException::class);

        $this->handler->handleGet($createdSpot->id);
    }

    // ----------------------------------------------------------------------------
    // Get Spot List Tests
    // ----------------------------------------------------------------------------
    public function testHandleGetSpotCollectionReturnsSpotList(): void
    {
        $this->authenticateTestUser(self::USER_EMAIL);

        $result = $this->handler->handleGetSpotCollection();

        $this->assertInstanceOf(SpotCollectionDTO::class, $result);
        $this->assertGreaterThanOrEqual(1, count($result->features));
    }

    public function testHandleGetSpotCollectionReturnsEmptyResult(): void
    {
        // Create a user without a spot
        $user = $this->createUser('ghost@example.com', 'Ghost', 112);
        $this->em->flush();

        $this->authenticateTestUser($user->getEmail());

        $result = $this->handler->handleGetSpotCollection();

        $this->assertInstanceOf(SpotCollectionDTO::class, $result);
        $this->assertEquals(0, count($result->features));
    }

    // ----------------------------------------------------------------------------
    // Get SpotList of friends Tests
    // ----------------------------------------------------------------------------
    public function testHandleGetSpotFriendsCollectionReturnsFriendsSpots(): void
    {
        // Create two users with a friendship relation
        $user = $this->createUser('user_a@example.com', 'UserA', 112);
        $friend = $this->createUser('user_b@example.com', 'UserB', 110);

        $this->createFriendship($friend, $user);
        $this->em->flush();

        // Create a spot for the friend
        $createdSpot = $this->createSpotForUser($friend->getEmail());

        // Test the handleGetSpotFriendsCollection method
        $this->authenticateTestUser($user->getEmail());

        $result = $this->handler->handleGetSpotFriendsCollection();

        $this->assertInstanceOf(SpotCollectionDTO::class, $result);
        $this->assertGreaterThanOrEqual(1, count($result->features));

        // Check the spot data
        $friendSpotId = $result->features[0]->properties->spotId;
        $this->assertEquals($createdSpot->id, $friendSpotId);
    }

    // ----------------------------------------------------------------------------
    // Get Spot by Id of a friend Tests
    // ----------------------------------------------------------------------------
    public function testHandleGetSpotFriendSuccess(): void
    {
        // Create two users with a friendship relation
        $user = $this->createUser('user_c@example.com', 'UserC', 113);
        $friend = $this->createUser('user_d@example.com', 'UserD', 114);
        $this->createFriendship($friend, $user);
        $this->em->flush();

        // Create a spot for the friend
        $createdSpot = $this->createSpotForUser($friend->getEmail());

        // Authenticate the user (who is a friend)
        $this->authenticateTestUser($user->getEmail());

        // Call
        $result = $this->handler->handleGetSpotFriend($createdSpot->id);

        $this->assertEquals($createdSpot->id, $result->id);
        $this->assertEquals($friend->getPseudo(), $result->owner->pseudo);
    }

    public function testHandleGetSpotFriendAccessDeniedException(): void
    {
        // Create two users without a friendship relation
        $user = $this->createUser('user_e@example.com', 'UserE', 115);
        $other = $this->createUser('user_f@example.com', 'UserF', 116);
        $this->em->flush();

        // Create a spot for the other user
        $createdSpot = $this->createSpotForUser($other->getEmail());

        // Authenticate the user who is not a friend
        $this->authenticateTestUser($user->getEmail());

        $this->expectException(SpotAccessDeniedException::class);

        $this->handler->handleGetSpotFriend($createdSpot->id);
    }

    public function testHandleGetSpotFriendNotFoundException(): void
    {
        $this->authenticateTestUser(self::USER_EMAIL);

        $this->expectException(SpotNotFoundException::class);

        $this->handler->handleGetSpotFriend(9999);
    }

    // ----------------------------------------------------------------------------
    // Private functions
    // ----------------------------------------------------------------------------
    private function authenticateTestUser(string $userEmail): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userEmail]);
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main'));

        return $user;
    }

    private function createSpotForUser(string $emailUser): SpotDTO
    {
        $this->authenticateTestUser($emailUser);

        return $this->handler->handleCreate($this->createSpotDTO(self::SPOT_LONGITUDE, self::SPOT_LATITUDE, self::SPOT_DESCRIPTION));
    }

    private function createSpotDTO(float $longitude, float $latitude, string $description): SpotDTO
    {
        return new SpotDTO(
            longitude: $longitude,
            latitude: $latitude,
            description: $description,
            owner: null,
            isFavorite: false,
            id: null
        );
    }

    private function createUser(string $email, string $pseudo, int $id): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPseudo($pseudo);
        $user->setPassword('password');
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTime());
        $user->setEmailVerified(true);
        $user->setId($id);

        $this->em->persist($user);

        return $user;
    }

    private function createFriendship(User $requester, User $receiver): Friendship
    {
        $friendship = new Friendship();
        $friendship->setRequester($requester);
        $friendship->setReceiver($receiver);
        $friendship->setConfirmed(true);

        $this->em->persist($friendship);

        return $friendship;
    }
}

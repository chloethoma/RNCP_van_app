<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\FriendshipDataTransformer;
use App\DTO\Friendship\FriendshipDTO;
use App\DTO\Friendship\FriendshipUserDTO;
use App\Entity\Friendship;
use App\Entity\User;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;

class FriendshipDataTransformerTest extends TestCase
{
    private const REQUESTER_ID = 1;
    private const RECEIVER_ID = 2;
    private const REQUESTER_PSEUDO = 'requester';
    private const RECEIVER_PSEUDO = 'receiver';
    private const REQUESTER_PICTURE = 'pic1';
    private const RECEIVER_PICTURE = 'pic2';

    private FriendshipDataTransformer $transformer;
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        $this->transformer = new FriendshipDataTransformer($this->validator);
    }

    public function testMapDTOToEntityWithNewEntity(): void
    {
        $dto = new FriendshipDTO(
            requester: new FriendshipUserDTO(self::REQUESTER_ID, self::REQUESTER_PSEUDO, self::REQUESTER_PICTURE),
            receiver: new FriendshipUserDTO(self::RECEIVER_ID, self::RECEIVER_PSEUDO, self::RECEIVER_PICTURE),
            isConfirmed: false
        );

        $this->transformer->setDTO($dto);
        $entity = $this->transformer->mapDTOToEntity();

        $this->assertInstanceOf(Friendship::class, $entity);
        $this->assertEquals(false, $entity->isConfirmed());
    }

    public function testMapDTOToEntityWithExistingEntity(): void
    {
        $existingEntity = new Friendship();
        $existingEntity->setConfirmed(false);

        $dto = new FriendshipDTO(
            requester: new FriendshipUserDTO(self::REQUESTER_ID, self::REQUESTER_PSEUDO, self::REQUESTER_PICTURE),
            receiver: new FriendshipUserDTO(self::RECEIVER_ID, self::RECEIVER_PSEUDO, self::RECEIVER_PICTURE),
            isConfirmed: true
        );

        $this->transformer->setEntity($existingEntity);
        $this->transformer->setDTO($dto);
        $entity = $this->transformer->mapDTOToEntity();

        $this->assertSame($existingEntity, $entity);
        $this->assertTrue($entity->isConfirmed());
    }

    public function testMapEntityToDTO(): void
    {
        $requester = $this->createUser(self::REQUESTER_ID, self::REQUESTER_PSEUDO, self::REQUESTER_PICTURE);
        $receiver = $this->createUser(self::RECEIVER_ID, self::RECEIVER_PSEUDO, self::RECEIVER_PICTURE);

        $entity = $this->createFriendship($requester, $receiver, true);

        $this->validator
            ->expects($this->exactly(1))
            ->method('validate');

        $this->transformer->setEntity($entity);
        $dto = $this->transformer->mapEntityToDTO();

        $this->assertInstanceOf(FriendshipDTO::class, $dto);
        $this->assertTrue($dto->isConfirmed);
        $this->assertEquals(self::REQUESTER_ID, $dto->requester->id);
        $this->assertEquals(self::RECEIVER_ID, $dto->receiver->id);
    }

    private function createUser(int $id, string $pseudo, string $picture): User
    {
        return (new User())
            ->setId($id)
            ->setPseudo($pseudo)
            ->setPicture($picture);
    }

    private function createFriendship(User $requester, User $receiver, bool $confirmed): Friendship
    {
        return (new Friendship())
            ->setRequester($requester)
            ->setReceiver($receiver)
            ->setConfirmed($confirmed);
    }
}

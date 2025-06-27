<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\PartialFriendshipListDataTransformer;
use App\DTO\Friendship\PartialFriendshipDTO;
use App\Entity\Friendship;
use App\Entity\FriendshipCollection;
use App\Entity\User;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;

class PartialFriendshipListDataTransformerTest extends TestCase
{
    private const REQUESTER_ID = 10;
    private const RECEIVER_ID = 20;
    private const REQUESTER_PSEUDO = 'requester';
    private const RECEIVER_PSEUDO = 'receiver';
    private const REQUESTER_PICTURE = 'pic1';
    private const RECEIVER_PICTURE = 'pic2';

    private PartialFriendshipListDataTransformer $transformer;
    private $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        $this->transformer = new PartialFriendshipListDataTransformer($this->validator);
    }

    public function testTransformArrayToObjectList(): void
    {
        $friendship1 = $this->createMock(Friendship::class);
        $friendship2 = $this->createMock(Friendship::class);
        $array = [$friendship1, $friendship2];

        $collection = $this->transformer->transformArrayToObjectList($array);

        $this->assertInstanceOf(FriendshipCollection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertSame($friendship1, $collection[0]);
        $this->assertSame($friendship2, $collection[1]);
    }

    public function testMapEntityListToDTOListRequester(): void
    {
        $requester = $this->createUser(self::REQUESTER_ID, self::REQUESTER_PSEUDO, self::REQUESTER_PICTURE);
        $receiver = $this->createUser(self::RECEIVER_ID, self::RECEIVER_PSEUDO, self::RECEIVER_PICTURE);

        $collection = $this->createFriendshipCollection($requester, $receiver, [true, true]);

        $this->transformer->setEntityList($collection);

        $this->validator->expects($this->exactly(2))->method('validate');

        $result = $this->transformer->mapEntityListToDTOList(self::REQUESTER_ID);

        $this->assertCount(2, $result);
        $dto = $result[0];
        $this->assertInstanceOf(PartialFriendshipDTO::class, $dto);
        $this->assertEquals(self::RECEIVER_ID, $dto->friend->id);
        $this->assertTrue($dto->isConfirmed);
    }

    public function testMapEntityListToDTOListReceiver(): void
    {
        $requester = $this->createUser(self::REQUESTER_ID, self::REQUESTER_PSEUDO, self::REQUESTER_PICTURE);
        $receiver = $this->createUser(self::RECEIVER_ID, self::RECEIVER_PSEUDO, self::RECEIVER_PICTURE);

        $collection = $this->createFriendshipCollection($requester, $receiver, [false, false]);

        $this->transformer->setEntityList($collection);

        $this->validator->expects($this->exactly(2))->method('validate');

        $result = $this->transformer->mapEntityListToDTOList(self::RECEIVER_ID);

        $this->assertCount(2, $result);
        $dto = $result[0];
        $this->assertInstanceOf(PartialFriendshipDTO::class, $dto);
        $this->assertEquals(self::REQUESTER_ID, $dto->friend->id);
        $this->assertFalse($dto->isConfirmed);
    }

    private function createUser(int $id, string $pseudo, string $picture): User
    {
        return (new User())->setId($id)->setPseudo($pseudo)->setPicture($picture);
    }

    private function createFriendship(User $requester, User $receiver, bool $confirmed): Friendship
    {
        return (new Friendship())->setRequester($requester)->setReceiver($receiver)->setConfirmed($confirmed);
    }

    private function createFriendshipCollection(User $requester, User $receiver, array $confirmations): FriendshipCollection
    {
        $collection = new FriendshipCollection();
        foreach ($confirmations as $confirmed) {
            $collection->append($this->createFriendship($requester, $receiver, $confirmed));
        }

        return $collection;
    }
}

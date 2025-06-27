<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\UserListDataTransformer;
use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Entity\UserCollection;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;

class UserListDataTransformerTest extends TestCase
{
    private const USER1_ID = 1;
    private const USER1_EMAIL = 'test1@example.com';
    private const USER1_PSEUDO = 'pseudo1';
    private const USER1_PICTURE = 'picture1.jpg';
    private const USER1_TOKEN = 'token1';
    private const USER1_CREATED_AT = '2023-01-01';
    private const USER1_UPDATED_AT = '2023-06-01';

    private const USER2_ID = 2;
    private const USER2_EMAIL = 'test2@example.com';
    private const USER2_PSEUDO = 'pseudo2';
    private const USER2_PICTURE = 'picture2.jpg';
    private const USER2_TOKEN = 'token2';
    private const USER2_CREATED_AT = '2023-02-01';
    private const USER2_UPDATED_AT = '2023-07-01';

    private UserListDataTransformer $transformer;
    private $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        $this->transformer = new UserListDataTransformer($this->validator);
    }

    public function testTransformArrayToObjectList(): void
    {
        $user1 = new User();
        $user2 = new User();

        $result = $this->transformer->transformArrayToObjectList([$user1, $user2]);

        $this->assertInstanceOf(UserCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($user1, $result[0]);
        $this->assertSame($user2, $result[1]);
    }

    public function testMapEntityListToDTOList(): void
    {
        $user1 = $this->createUser(
            self::USER1_ID,
            self::USER1_EMAIL,
            true,
            self::USER1_PSEUDO,
            self::USER1_CREATED_AT,
            self::USER1_UPDATED_AT,
            self::USER1_PICTURE,
            self::USER1_TOKEN
        );

        $user2 = $this->createUser(
            self::USER2_ID,
            self::USER2_EMAIL,
            false,
            self::USER2_PSEUDO,
            self::USER2_CREATED_AT,
            self::USER2_UPDATED_AT,
            self::USER2_PICTURE,
            self::USER2_TOKEN
        );

        $collection = new UserCollection();
        $collection->append($user1);
        $collection->append($user2);

        $this->transformer->setEntityList($collection);

        $this->validator
            ->expects($this->exactly(2))
            ->method('validate')
            ->with($this->isInstanceOf(UserDTO::class), UserDTO::class);

        $result = $this->transformer->mapEntityListToDTOList();

        $this->assertInstanceOf(\ArrayObject::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(UserDTO::class, $result[0]);
        $this->assertInstanceOf(UserDTO::class, $result[1]);
    }

    private function createUser(
        int $id,
        string $email,
        bool $verified,
        string $pseudo,
        string $createdAt,
        string $updatedAt,
        string $picture,
        string $token,
    ): User {
        $user = new User();
        $user->setId($id);
        $user->setEmail($email);
        $user->setEmailVerified($verified);
        $user->setPseudo($pseudo);
        $user->setCreatedAt(new \DateTimeImmutable($createdAt));
        $user->setUpdatedAt(new \DateTime($updatedAt));
        $user->setPicture($picture);
        $user->setToken($token);

        return $user;
    }
}

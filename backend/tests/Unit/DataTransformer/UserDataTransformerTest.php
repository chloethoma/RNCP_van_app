<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\UserDataTransformer;
use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;

class UserDataTransformerTest extends TestCase
{
    private const USER_ID = 1;
    private const EMAIL = 'test@example.com';
    private const NEW_EMAIL = 'new@example.com';
    private const PASSWORD = 'secret';
    private const PSEUDO = 'pseudo';
    private const NEW_PSEUDO = 'newPseudo';
    private const PICTURE = 'picture.jpg';
    private const NEW_PICTURE = 'newPicture.jpg';
    private const TOKEN = 'token';
    private const CREATED_AT = '2020-01-01';
    private const CREATED_AT_OBJ = '2023-01-01';
    private const UPDATED_AT_OBJ = '2023-06-01';

    private UserDataTransformer $transformer;
    private $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        $this->transformer = new UserDataTransformer($this->validator);
    }

    public function testMapDTOToEntityCreate(): void
    {
        $dto = new UserDTO(
            id: null,
            email: self::EMAIL,
            pseudo: self::PSEUDO,
            token: null,
            createdAt: null,
            updatedAt: null,
            picture: self::PICTURE,
            emailVerified: true,
            password: self::PASSWORD,
        );

        $this->transformer->setDTO($dto);
        $user = $this->transformer->mapDTOToEntity();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(self::EMAIL, $user->getEmail());
        $this->assertEquals(true, $user->isEmailVerified());
        $this->assertEquals(self::PSEUDO, $user->getPseudo());
        $this->assertEquals(self::PICTURE, $user->getPicture());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $user->getUpdatedAt());
    }

    public function testMapDTOToEntityUpdate(): void
    {
        $dto = new UserDTO(
            id: self::USER_ID,
            email: self::NEW_EMAIL,
            pseudo: self::NEW_PSEUDO,
            token: null,
            createdAt: null,
            updatedAt: null,
            picture: self::NEW_PICTURE,
            emailVerified: false,
            password: null,
        );

        $existingUser = new User();
        $existingUser->setId(self::USER_ID);
        $existingUser->setCreatedAt(new \DateTimeImmutable(self::CREATED_AT));

        $this->transformer->setEntity($existingUser);
        $this->transformer->setDTO($dto);
        $updatedUser = $this->transformer->mapDTOToEntity();

        $this->assertSame($existingUser, $updatedUser);
        $this->assertEquals(self::NEW_EMAIL, $updatedUser->getEmail());
        $this->assertEquals(false, $updatedUser->isEmailVerified());
        $this->assertEquals(self::NEW_PSEUDO, $updatedUser->getPseudo());
        $this->assertEquals(self::NEW_PICTURE, $updatedUser->getPicture());
        $this->assertEquals(new \DateTimeImmutable(self::CREATED_AT), $updatedUser->getCreatedAt());
    }

    public function testMapEntityToDTO(): void
    {
        $entity = new User();
        $entity->setId(self::USER_ID);
        $entity->setEmail(self::EMAIL);
        $entity->setEmailVerified(true);
        $entity->setPseudo(self::PSEUDO);
        $entity->setCreatedAt(new \DateTimeImmutable(self::CREATED_AT_OBJ));
        $entity->setUpdatedAt(new \DateTime(self::UPDATED_AT_OBJ));
        $entity->setPicture(self::PICTURE);
        $entity->setToken(self::TOKEN);

        $this->transformer->setEntity($entity);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(UserDTO::class), UserDTO::class, ['read']);

        $dto = $this->transformer->mapEntityToDTO();

        $this->assertInstanceOf(UserDTO::class, $dto);
        $this->assertEquals(self::EMAIL, $dto->email);
        $this->assertEquals(self::PSEUDO, $dto->pseudo);
        $this->assertEquals(self::PICTURE, $dto->picture);
        $this->assertEquals(self::TOKEN, $dto->token);
    }
}

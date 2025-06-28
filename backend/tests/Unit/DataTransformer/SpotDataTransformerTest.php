<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\SpotDataTransformer;
use App\DTO\Spot\SpotDTO;
use App\DTO\Spot\SpotOwnerDTO;
use App\Entity\Spot;
use App\Entity\User;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;

class SpotDataTransformerTest extends TestCase
{
    private const SPOT_ID = 10;
    private const LATITUDE = 48.8566;
    private const LONGITUDE = 2.3522;
    private const DESCRIPTION = 'Paris';
    private const IS_FAVORITE = true;
    private const OWNER_ID = 1;
    private const OWNER_PSEUDO = 'owner';
    private const OWNER_PICTURE = 'pic.jpg';

    private SpotDataTransformer $transformer;
    private $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        $this->transformer = new SpotDataTransformer($this->validator);
    }

    private function createUser(): User
    {
        return (new User())
            ->setId(self::OWNER_ID)
            ->setPseudo(self::OWNER_PSEUDO)
            ->setPicture(self::OWNER_PICTURE);
    }

    public function testMapDTOtoEntity(): void
    {
        $dto = new SpotDTO(
            id: null,
            latitude: self::LATITUDE,
            longitude: self::LONGITUDE,
            description: self::DESCRIPTION,
            isFavorite: self::IS_FAVORITE,
            owner: new SpotOwnerDTO(id: self::OWNER_ID, pseudo: self::OWNER_PSEUDO, picture: self::OWNER_PICTURE)
        );

        $this->transformer->setDTO($dto);
        $entity = $this->transformer->mapDTOtoEntity();

        $this->assertInstanceOf(Spot::class, $entity);
        $this->assertEquals(self::LATITUDE, $entity->getLatitude());
        $this->assertEquals(self::LONGITUDE, $entity->getLongitude());
        $this->assertEquals(self::DESCRIPTION, $entity->getDescription());
        $this->assertTrue($entity->isFavorite());
    }

    public function testMapEntityToDTO(): void
    {
        $user = $this->createUser();

        $entity = new Spot();
        $entity->setId(self::SPOT_ID);
        $entity->setLatitude(self::LATITUDE);
        $entity->setLongitude(self::LONGITUDE);
        $entity->setDescription(self::DESCRIPTION);
        $entity->setIsFavorite(self::IS_FAVORITE);
        $entity->setOwner($user);

        $this->transformer->setEntity($entity);

        $this->validator->expects($this->once())->method('validate');

        $dto = $this->transformer->mapEntityToDTO();

        $this->assertInstanceOf(SpotDTO::class, $dto);
        $this->assertEquals(self::SPOT_ID, $dto->id);
        $this->assertEquals(self::LATITUDE, $dto->latitude);
        $this->assertEquals(self::LONGITUDE, $dto->longitude);
        $this->assertEquals(self::DESCRIPTION, $dto->description);
        $this->assertTrue($dto->isFavorite);
        $this->assertEquals(self::OWNER_ID, $dto->owner->id);
        $this->assertEquals(self::OWNER_PSEUDO, $dto->owner->pseudo);
    }
}

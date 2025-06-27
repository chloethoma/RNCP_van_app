<?php

namespace App\Tests\DataTransformer;

use App\DataTransformer\SpotListDataTransformer;
use App\DTO\SpotGeoJson\SpotCollectionDTO;
use App\Entity\Spot;
use App\Entity\SpotCollection;
use App\Entity\User;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;

class SpotListDataTransformerTest extends TestCase
{
    private const OWNER_ID = 1;
    private const SPOT1_ID = 10;
    private const SPOT1_LAT = 48.8566;
    private const SPOT1_LON = 2.3522;
    private const SPOT2_ID = 20;
    private const SPOT2_LAT = 43.2965;
    private const SPOT2_LON = 5.3698;

    private SpotListDataTransformer $transformer;
    private $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(Validator::class);
        $this->transformer = new SpotListDataTransformer($this->validator);
    }

    public function testMapEntityListToDTOList(): void
    {
        $user = $this->createUser();

        $spot1 = $this->createSpot(self::SPOT1_ID, self::SPOT1_LAT, self::SPOT1_LON, $user);
        $spot2 = $this->createSpot(self::SPOT2_ID, self::SPOT2_LAT, self::SPOT2_LON, $user);

        $collection = $this->createSpotCollection($spot1, $spot2);

        $this->transformer->setEntityList($collection);

        $this->validator->expects($this->once())->method('validate');

        $dtoCollection = $this->transformer->mapEntityListToDTOList();

        $this->assertInstanceOf(SpotCollectionDTO::class, $dtoCollection);
        $this->assertCount(2, $dtoCollection->features);

        $dtoSpot1 = $dtoCollection->features[0];
        $this->assertEquals([self::SPOT1_LON, self::SPOT1_LAT], $dtoSpot1->geometry->coordinates);
        $this->assertEquals(self::SPOT1_ID, $dtoSpot1->properties->spotId);
        $this->assertEquals(self::OWNER_ID, $dtoSpot1->properties->ownerId);
    }

    public function testMapEntityListToDTOListWithEmptyCollection(): void
    {
        $this->transformer->setEntityList(new SpotCollection());

        $this->validator->expects($this->once())->method('validate');

        $dtoCollection = $this->transformer->mapEntityListToDTOList();

        $this->assertInstanceOf(SpotCollectionDTO::class, $dtoCollection);
        $this->assertEmpty($dtoCollection->features);
        $this->assertIsArray($dtoCollection->features);
        $this->assertSame([], $dtoCollection->features);
    }

    public function testTransformArrayToObjectList(): void
    {
        $spot1 = $this->createMock(Spot::class);
        $spot2 = $this->createMock(Spot::class);
        $array = [$spot1, $spot2];

        $collection = $this->transformer->transformArrayToObjectList($array);

        $this->assertInstanceOf(SpotCollection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertSame($spot1, $collection[0]);
        $this->assertSame($spot2, $collection[1]);
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setId(self::OWNER_ID);

        return $user;
    }

    private function createSpot(int $id, float $lat, float $lon, User $owner): Spot
    {
        $spot = new Spot();
        $spot->setId($id);
        $spot->setLatitude($lat);
        $spot->setLongitude($lon);
        $spot->setOwner($owner);

        return $spot;
    }

    private function createSpotCollection(Spot ...$spots): SpotCollection
    {
        $collection = new SpotCollection();
        foreach ($spots as $spot) {
            $collection->append($spot);
        }

        return $collection;
    }
}

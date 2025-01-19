<?php

namespace App\Tests\Service;

use App\DTO\Feature\SpotFeatureCollection;
use App\Entity\Spot;
use App\Output\SpotFeature;
use App\Output\SpotGeometry;
use App\Output\SpotProperties;
use App\Service\DataTransformer\SpotDataTransformer;
use App\Service\Validator\Validator;
use PHPUnit\Framework\TestCase;

class SpotDataTransformerTest extends TestCase
{
    public function testTransformToFeatureCollection(): void
    {
        // Mock du Validator
        $validatorMock = $this->createMock(Validator::class);
        $validatorMock->expects($this->once())
            ->method('validate')
            ->with(
                $this->isInstanceOf(SpotFeatureCollection::class),
                SpotFeatureCollection::class
            );

        // Création d'entités Spot simulées
        $spot1 = $this->createMock(Spot::class);
        $spot1->method('getLongitude')->willReturn(2.3522);
        $spot1->method('getLatitude')->willReturn(48.8566);
        $spot1->method('getId')->willReturn(1);

        $spot2 = $this->createMock(Spot::class);
        $spot2->method('getLongitude')->willReturn(-0.1276);
        $spot2->method('getLatitude')->willReturn(51.5074);
        $spot2->method('getId')->willReturn(2);

        // Tableau des entités Spot
        $spotsEntities = [$spot1, $spot2];

        // Instanciation du transformer
        $transformer = new SpotDataTransformer($validatorMock);

        // Exécution de la méthode à tester
        $result = $transformer->transformToFeatureCollection($spotsEntities);

        // Assertions
        $this->assertInstanceOf(SpotFeatureCollection::class, $result);
        $this->assertCount(2, $result->features);

        // $feature1 = $result->features[0];
        // $this->assertInstanceOf(SpotFeature::class, $feature1);
        // $this->assertInstanceOf(SpotGeometry::class, $feature1->getGeometry());
        // $this->assertInstanceOf(SpotProperties::class, $feature1->getProperties());
        // $this->assertEquals(2.3522, $feature1->getGeometry()->getLongitude());
        // $this->assertEquals(48.8566, $feature1->getGeometry()->getLatitude());
        // $this->assertEquals(1, $feature1->getProperties()->getId());
    }
}

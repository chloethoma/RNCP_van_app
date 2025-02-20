<?php

namespace App\Tests\Unit\Controller;

use App\Controller\SpotController;
use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Feature\SpotFeatureDTO;
use App\DTO\Feature\SpotGeometryDTO;
use App\DTO\Feature\SpotPropertiesDTO;
use App\DTO\Spot\SpotDTO;
use App\Handler\SpotHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SpotControllerTest extends KernelTestCase
{
    private SpotController $controller;
    private $handlerMock;
    private $loggerMock;
    private $urlGeneratorMock;

    public function setUp(): void
    {
        self::bootKernel();

        $this->handlerMock = $this->createMock(SpotHandler::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->controller = new SpotController(
            $this->loggerMock,
            $this->handlerMock
        );

        $this->controller->setContainer(self::getContainer());
    }

    public function testCreateSpot(): void
    {
        $spotDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
        );

        $createdSpotDTO = $this->getSpotDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleCreate')
            ->with($spotDTO)
            ->willReturn($createdSpotDTO);

        $response = $this->controller->createSpot($spotDTO, $this->urlGeneratorMock);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(json_encode($createdSpotDTO), $response->getContent());
    }

    #[DataProvider('createSpotExceptionDataProvider')]
    public function testCreateSpotException(\Throwable $exception, int $expectedStatusCode): void
    {
        $spotDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
        );

        $this->handlerMock
            ->expects($this->once())
            ->method('handleCreate')
            ->with($spotDTO)
            ->willThrowException($exception);

        if (!$exception instanceof HttpExceptionInterface) {
            $this->loggerMock
                ->expects($this->once())
                ->method('error');
        }

        $response = $this->controller->createSpot($spotDTO, $this->urlGeneratorMock);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function createSpotExceptionDataProvider()
    {
        return [
            'Not found Exception' => [new NotFoundHttpException(), 404],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testGetSpots(): void
    {
        $expectedDTO = $this->getFeatureCollectionDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleGetFeatureCollection')
            ->willReturn($expectedDTO);

        $response = $this->controller->getSpots();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedDTO), $response->getContent());
    }

    #[DataProvider('getSpotsExceptionDataProvider')]
    public function testGetSpotsException(\Throwable $exception, int $expectedStatusCode): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleGetFeatureCollection')
            ->willThrowException($exception);

        if (!$exception instanceof HttpExceptionInterface) {
            $this->loggerMock
                ->expects($this->once())
                ->method('error');
        }

        $response = $this->controller->getSpots();

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function getSpotsExceptionDataProvider()
    {
        return [
            'Not found' => [new NotFoundHttpException(), 404],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testGetSpot(): void
    {
        $expectedDTO = $this->getSpotDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleGet')
            ->with(1)
            ->willReturn($expectedDTO);

        $response = $this->controller->getSpot(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedDTO), $response->getContent());
    }

    #[DataProvider('getSpotExceptionDataProvider')]
    public function testGetSpotException(\Throwable $exception, int $expectedStatusCode): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleGet')
            ->with(1)
            ->willThrowException($exception);

        if (!$exception instanceof HttpExceptionInterface) {
            $this->loggerMock
                ->expects($this->once())
                ->method('error');
        }

        $response = $this->controller->getSpot(1);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function getSpotExceptionDataProvider()
    {
        return [
            'Not found' => [new NotFoundHttpException(), 404],
            'AccessDenied' => [new AccessDeniedHttpException(), 403],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testUpdateSpot(): void
    {
        $spotDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
        );

        $expectedSpotDTO = $this->getSpotDTO();

        $this->handlerMock
            ->expects($this->once())
            ->method('handleUpdate')
            ->with($spotDTO, 1)
            ->willReturn($expectedSpotDTO);

        $response = $this->controller->updateSpot($spotDTO, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedSpotDTO), $response->getContent());
    }

    #[DataProvider('updateSpotExceptionDataProvider')]
    public function testUpdateSpotException(\Throwable $exception, int $expectedStatusCode): void
    {
        $spotDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
        );

        $this->handlerMock
            ->expects($this->once())
            ->method('handleUpdate')
            ->with($spotDTO, 1)
            ->willThrowException($exception);

        if (!$exception instanceof HttpExceptionInterface) {
            $this->loggerMock
                ->expects($this->once())
                ->method('error');
        }

        $response = $this->controller->updateSpot($spotDTO, 1);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function updateSpotExceptionDataProvider()
    {
        return [
            'Not found Exception' => [new NotFoundHttpException(), 404],
            'AccessDenied' => [new AccessDeniedHttpException(), 403],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    public function testDeleteSpot(): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleDelete')
            ->with(1);

        $response = $this->controller->deleteSpot(1);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('null', $response->getContent());
    }

    #[DataProvider('deleteSpotExceptionDataProvider')]
    public function testDeleteSpotException(\Throwable $exception, int $expectedStatusCode): void
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('handleDelete')
            ->with(1)
            ->willThrowException($exception);

        $response = $this->controller->deleteSpot(1);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    public static function deleteSpotExceptionDataProvider()
    {
        return [
            'Not found Exception' => [new NotFoundHttpException(), 404],
            'AccessDenied' => [new AccessDeniedHttpException(), 403],
            'Generic Exception' => [new \Exception(), 500],
        ];
    }

    private function getSpotDTO(): SpotDTO
    {
        return new SpotDTO(
            id: 1,
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
            userId: 2
        );
    }

    private function getFeatureCollectionDTO(): SpotFeatureCollectionDTO
    {
        $feature1 = new SpotFeatureDTO(
            properties: new SpotPropertiesDTO(
                id: 1
            ),
            geometry: new SpotGeometryDTO(
                coordinates: [10.685141, 40.421539],
                type: 'Point'
            ),
            type: 'Feature'
        );

        $feature2 = new SpotFeatureDTO(
            properties: new SpotPropertiesDTO(
                id: 2
            ),
            geometry: new SpotGeometryDTO(
                coordinates: [20.685141, 50.421539],
                type: 'Point'
            ),
            type: 'Feature'
        );

        $featureCollection = [];
        $featureCollection[] = $feature1;
        $featureCollection[] = $feature2;

        return new SpotFeatureCollectionDTO(
            type: 'FeatureCollection',
            features: $featureCollection
        );
    }
}

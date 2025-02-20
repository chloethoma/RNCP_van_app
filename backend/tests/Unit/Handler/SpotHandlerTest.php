<?php

namespace App\Tests\Unit\Handler;

use App\DataTransformer\FeatureDataTransformer;
use App\DataTransformer\SpotDataTransformer;
use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Feature\SpotFeatureDTO;
use App\DTO\Feature\SpotGeometryDTO;
use App\DTO\Feature\SpotPropertiesDTO;
use App\DTO\Spot\SpotDTO;
use App\Entity\Spot;
use App\Entity\SpotCollection;
use App\Entity\User;
use App\Handler\SpotHandler;
use App\Manager\SpotManager;
use App\Manager\UserManager;
use App\Repository\SpotRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpotHandlerTest extends KernelTestCase
{
    private SpotHandler $handler;
    private SpotDataTransformer $spotTransformer;
    private FeatureDataTransformer $featureTransformer;

    private $repositoryMock;
    private $spotManagerMock;
    private $userManagerMock;

    public function setup(): void
    {
        self::bootKernel();

        $this->repositoryMock = $this->createMock(SpotRepository::class);
        $this->spotManagerMock = $this->createMock(SpotManager::class);
        $this->userManagerMock = $this->createMock(UserManager::class);

        $container = self::getContainer();

        $this->spotTransformer = $container->get(SpotDataTransformer::class);
        $this->featureTransformer = $container->get(FeatureDataTransformer::class);

        $this->handler = new SpotHandler(
            $this->repositoryMock,
            $this->spotTransformer,
            $this->spotManagerMock,
            $this->featureTransformer,
            $this->userManagerMock
        );
    }

    public function testHandleCreate(): void
    {
        $spotInputDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
        );

        $this->spotManagerMock
            ->expects($this->once())
            ->method('initSpotOwner')
            ->willReturn($this->getSpotEntity());

        $this->repositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->getSpotEntity());

        $spotOutputDTO = $this->handler->handleCreate($spotInputDTO);

        $expectedDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
            id: 1,
            userId: 10
        );

        $this->assertEquals($expectedDTO, $spotOutputDTO);
    }

    public function testHandleCreateNotFoundException(): void
    {
        $spotInputDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
        );

        $this->spotManagerMock
            ->expects($this->once())
            ->method('initSpotOwner')
            ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleCreate($spotInputDTO);
    }

    public function testHandleGet(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->getSpotEntity());

        $this->spotManagerMock
            ->expects($this->once())
            ->method('checkAccess');

        $spotOutputDTO = $this->handler->handleGet(1);

        $expectedDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
            id: 1,
            userId: 10
        );

        $this->assertEquals($expectedDTO, $spotOutputDTO);
    }

    public function testHandleGetNotFoundException(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleGet(1);
    }

    public function testHandleGetAccessDeniedException(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->getSpotEntity());

        $this->spotManagerMock
            ->expects($this->once())
            ->method('checkAccess')
            ->willThrowException(new AccessDeniedHttpException());

        $this->expectException(AccessDeniedHttpException::class);

        $this->handler->handleGet(1);
    }

    public function testHandleUpdate(): void
    {
        $spotInputDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->getSpotEntity());

        $this->spotManagerMock
            ->expects($this->once())
            ->method('checkAccess');

        $this->repositoryMock
            ->expects($this->once())
            ->method('update')
            ->willReturn($this->getSpotEntity());

        $spotOutputDTO = $this->handler->handleUpdate($spotInputDTO, 1);

        $expectedDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false,
            id: 1,
            userId: 10
        );

        $this->assertEquals($expectedDTO, $spotOutputDTO);
    }

    public function testHandleUpdateNotFoundException(): void
    {
        $spotInputDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleUpdate($spotInputDTO, 1);
    }

    public function testHandleUpdateAccessDeniedException(): void
    {
        $spotInputDTO = new SpotDTO(
            latitude: 45.421539,
            longitude: 5.685141,
            description: 'test',
            isFavorite: false
        );

        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->getSpotEntity());

        $this->spotManagerMock
            ->expects($this->once())
            ->method('checkAccess')
            ->willThrowException(new AccessDeniedHttpException());

        $this->expectException(AccessDeniedHttpException::class);

        $this->handler->handleUpdate($spotInputDTO, 1);
    }

    public function testHandleDelete(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->getSpotEntity());

        $this->spotManagerMock
            ->expects($this->once())
            ->method('checkAccess');

        $this->repositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->getSpotEntity());

        $this->handler->handleDelete(1);
    }

    public function testHandleDeleteNotFoundException(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleDelete(1);
    }

    public function testHandleDeleteAccessDeniedException(): void
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($this->getSpotEntity());

        $this->spotManagerMock
            ->expects($this->once())
            ->method('checkAccess')
            ->willThrowException(new AccessDeniedHttpException());

        $this->expectException(AccessDeniedHttpException::class);

        $this->handler->handleDelete(1);
    }

    public function testHandleGetFeatureCollection(): void
    {
        $this->userManagerMock
            ->expects($this->once())
            ->method('getOwner')
            ->willReturn(1);

        $this->repositoryMock
            ->expects($this->once())
            ->method('findCollection')
            ->with(1)
            ->willReturn($this->getSpotList());

        $featureOutputDTO = $this->handler->handleGetFeatureCollection();

        $expectedDTO = $this->getFeatureCollectionDTO();

        $this->assertEquals($expectedDTO, $featureOutputDTO);
    }

    public function testHandleGetFeatureCollectionNotFoundException(): void
    {
        $this->userManagerMock
            ->expects($this->once())
            ->method('getOwner')
            ->willReturn(1);

        $this->repositoryMock
            ->expects($this->once())
            ->method('findCollection')
            ->with(1)
            ->willThrowException(new NotFoundHttpException());

        $this->expectException(NotFoundHttpException::class);

        $this->handler->handleGetFeatureCollection();
    }

    private function getSpotEntity(): Spot
    {
        $owner = new User();
        $owner->setId(10);

        $spot = new Spot();
        $spot->setLatitude(45.421539);
        $spot->setLongitude(5.685141);
        $spot->setDescription('test');
        $spot->setIsFavorite(false);
        $spot->setOwner($owner);
        $spot->setId(1);

        return $spot;
    }

    private function getSpotList(): array
    {
        $owner = new User();
        $owner->setId(10);

        $spot1 = new Spot();
        $spot1->setLatitude(45.421539);
        $spot1->setLongitude(5.685141);
        $spot1->setDescription('test 1');
        $spot1->setIsFavorite(false);
        $spot1->setOwner($owner);
        $spot1->setId(1);

        $spot2 = new Spot();
        $spot2->setLatitude(55.421539);
        $spot2->setLongitude(15.685141);
        $spot2->setDescription('test 2');
        $spot2->setIsFavorite(true);
        $spot2->setOwner($owner);
        $spot2->setId(2);

        $spotList = [];
        $spotList[] = $spot1;
        $spotList[] = $spot2;

        return $spotList;
    }

    private function getFeatureCollectionDTO(): SpotFeatureCollectionDTO
    {
        $feature1 = new SpotFeatureDTO(
            properties: new SpotPropertiesDTO(
                id: 1
            ),
            geometry: new SpotGeometryDTO(
                coordinates: [5.685141, 45.421539],
                type: 'Point'
            ),
            type: 'Feature'
        );

        $feature2 = new SpotFeatureDTO(
            properties: new SpotPropertiesDTO(
                id: 2
            ),
            geometry: new SpotGeometryDTO(
                coordinates: [15.685141, 55.421539],
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

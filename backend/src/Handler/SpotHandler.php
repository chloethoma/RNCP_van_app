<?php

namespace App\Handler;

use App\DataTransformer\FeatureDataTransformer;
use App\DataTransformer\SpotDataTransformer;
use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Spot\SpotDTO;
use App\Manager\SpotManager;
use App\Manager\UserManager;
use App\Repository\SpotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpotHandler
{
    public function __construct(
        protected SpotRepository $repository,
        protected SpotDataTransformer $spotTransformer,
        protected SpotManager $spotManager,
        protected FeatureDataTransformer $featureTransformer,
        protected UserManager $userManager,
        protected EntityManagerInterface $em,
    ) {
    }

    public function handleCreate(SpotDTO $dto): SpotDTO
    {
        $this->spotTransformer->setDTO($dto);
        $spot = $this->spotTransformer->mapDTOtoEntity();

        $spot = $this->spotManager->initSpotOwner($spot);

        $this->em->persist($spot);
        $this->em->flush();

        $this->spotTransformer->setEntity($spot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleGet(int $spotId): SpotDTO
    {
        $spot = $this->repository->findById($spotId);

        if (!$spot) {
            throw new NotFoundHttpException();
        }

        $this->spotManager->checkAccess($spot);

        $this->spotTransformer->setEntity($spot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleUpdate(SpotDTO $dto, int $spotId): SpotDTO
    {
        $spot = $this->repository->findById($spotId);

        if (!$spot) {
            throw new NotFoundHttpException();
        }

        $this->spotManager->checkAccess($spot);

        $this->spotTransformer->setEntity($spot);
        $this->spotTransformer->setDTO($dto);
        $updatedSpot = $this->spotTransformer->mapDTOtoEntity();

        $this->em->flush();

        $this->spotTransformer->setEntity($updatedSpot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleDelete(int $spotId): void
    {
        $spot = $this->repository->findById($spotId);

        if (!$spot) {
            throw new NotFoundHttpException();
        }

        $this->spotManager->checkAccess($spot);

        $this->em->remove($spot);
        $this->em->flush();
    }

    public function handleGetFeatureCollection(): SpotFeatureCollectionDTO
    {
        $userId = $this->userManager->getOwner();

        $spotList = $this->repository->findCollection($userId);

        $spotCollection = $this->featureTransformer->transformArrayToObjectList($spotList);

        $this->featureTransformer->setEntityList($spotCollection);

        return $this->featureTransformer->mapEntityListToDTOList();
    }
}

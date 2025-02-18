<?php

namespace App\Handler;

use App\DataTransformer\FeatureDataTransformer;
use App\DataTransformer\SpotDataTransformer;
use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Spot\SpotDTO;
use App\Manager\SpotManager;
use App\Manager\UserManager;
use App\Repository\SpotRepository;

class SpotHandler
{
    public function __construct(
        protected SpotRepository $repository,
        protected SpotDataTransformer $spotTransformer,
        protected SpotManager $spotManager,
        protected FeatureDataTransformer $featureTransformer,
        protected UserManager $userManager,
    ) {
    }

    public function handleCreate(SpotDTO $dto): SpotDTO
    {
        $this->spotTransformer->setDTO($dto);
        $spot = $this->spotTransformer->mapDTOtoEntity();

        $spot = $this->spotManager->initSpotOwner($spot);

        $spot = $this->repository->create($spot);

        $this->spotTransformer->setEntity($spot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleGet(int $spotId): SpotDTO
    {
        $spot = $this->repository->findById($spotId);

        $this->spotManager->checkAccess($spot);

        $this->spotTransformer->setEntity($spot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleUpdate(SpotDTO $dto, int $spotId): SpotDTO
    {
        $spot = $this->repository->findById($spotId);

        $this->spotManager->checkAccess($spot);

        $this->spotTransformer->setEntity($spot);
        $this->spotTransformer->setDTO($dto);
        $spot = $this->spotTransformer->mapDTOtoEntity();

        $newSpot = $this->repository->update($spot);

        $this->spotTransformer->setEntity($newSpot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleDelete(int $spotId): void
    {
        $spot = $this->repository->findById($spotId);

        $this->spotManager->checkAccess($spot);

        $this->repository->delete($spot);
    }

    public function handleGetFeatureCollection(): SpotFeatureCollectionDTO
    {
        $userId = $this->userManager->getUserIdFromToken();

        $spotCollection = $this->repository->findCollection($userId);

        $this->featureTransformer->setEntityList($spotCollection);

        return $this->featureTransformer->mapEntityListToDTOList();
    }
}

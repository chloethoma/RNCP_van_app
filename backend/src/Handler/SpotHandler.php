<?php

namespace App\Handler;

use App\DataTransformer\FeatureDataTransformer;
use App\DataTransformer\SpotDataTransformer;
use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Spot\SpotDTO;
use App\Repository\SpotRepository;
use App\Service\Manager\SpotManager;
use App\Service\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpotHandler
{
    public function __construct(
        protected SpotRepository $repository,
        protected EntityManagerInterface $em,
        protected SpotDataTransformer $spotTransformer,
        protected SpotManager $spotManager,
        protected FeatureDataTransformer $featureTransformer,
        protected UserManager $userManager,
    ) {
    }

    public function handleCreate(SpotDTO $dto): SpotDTO
    {
        $spot = $this->spotTransformer->mapDTOtoEntity($dto);

        $spot = $this->spotManager->initSpotOwner($spot);

        $spot = $this->repository->createSpot($spot);

        return $this->spotTransformer->mapEntityToDTO($spot);
    }

    public function handleGet(int $spotId): SpotDTO
    {
        $userId = $this->userManager->getUserIdFromToken();

        $spot = $this->repository->getSpotById($spotId);

        if (!$spot) {
            throw new NotFoundHttpException();
        }

        if ($spot->getOwner()->getId() !== $userId) {
            throw new AccessDeniedHttpException();
        }

        return $this->spotTransformer->mapEntityToDTO($spot);
    }

    public function handleGetFeatureCollection(): SpotFeatureCollectionDTO
    {
        $userId = $this->userManager->getUserIdFromToken();

        $spotCollection = $this->repository->getSpotCollection($userId);

        return $this->featureTransformer->mapEntityListToDTOList($spotCollection);
    }
}

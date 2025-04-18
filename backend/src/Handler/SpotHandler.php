<?php

namespace App\Handler;

use App\DataTransformer\SpotDataTransformer;
use App\DataTransformer\SpotListDataTransformer;
use App\DTO\Spot\SpotDTO;
use App\DTO\SpotGeoJson\SpotCollectionDTO;
use App\Manager\SpotManager;
use App\Manager\UserManager;
use App\Repository\SpotRepository;
use App\Services\Exceptions\Spot\SpotNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class SpotHandler
{
    public function __construct(
        protected SpotRepository $repository,
        protected SpotDataTransformer $spotTransformer,
        protected SpotManager $spotManager,
        protected SpotListDataTransformer $spotListTransformer,
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
            throw new SpotNotFoundException();
        }

        $this->spotManager->checkAccess($spot);

        $this->spotTransformer->setEntity($spot);

        return $this->spotTransformer->mapEntityToDTO();
    }

    public function handleUpdate(SpotDTO $dto, int $spotId): SpotDTO
    {
        $spot = $this->repository->findById($spotId);

        if (!$spot) {
            throw new SpotNotFoundException();
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
            throw new SpotNotFoundException();
        }

        $this->spotManager->checkAccess($spot);

        $this->em->remove($spot);
        $this->em->flush();
    }

    public function handleGetSpotCollection(): SpotCollectionDTO
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $spotList = $this->repository->findCollection($userId);

        $spotCollection = $this->spotListTransformer->transformArrayToObjectList($spotList);

        $this->spotListTransformer->setEntityList($spotCollection);

        return $this->spotListTransformer->mapEntityListToDTOList();
    }

    public function handleGetSpotFriendsCollection(): SpotCollectionDTO
    {
        $userId = $this->userManager->getAuthenticatedUserId();

        $spotList = $this->repository->findFriendsSpots($userId);

        $spotCollection = $this->spotListTransformer->transformArrayToObjectList($spotList);

        $this->spotListTransformer->setEntityList($spotCollection);

        return $this->spotListTransformer->mapEntityListToDTOList();
    }

    public function handleGetSpotFriend(int $spotId): SpotDTO
    {
        $spot = $this->repository->findById($spotId);

        if (!$spot) {
            throw new SpotNotFoundException();
        }

        $this->spotManager->checkSpotFriendAccess($spot);

        $this->spotTransformer->setEntity($spot);

        return $this->spotTransformer->mapEntityToDTO();
    }
}

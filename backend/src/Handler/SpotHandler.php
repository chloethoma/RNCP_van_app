<?php

namespace App\Handler;

use App\DataTransformer\FeatureDataTransformer;
use App\DataTransformer\SpotDataTransformer;
use App\DTO\Feature\SpotFeatureCollectionDTO;
use App\DTO\Spot\SpotDTO;
use App\Entity\Spot;
use App\Repository\SpotRepository;
use App\Service\Manager\SpotManager;
use App\Service\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SpotHandler
{
    public function __construct(
        protected SpotRepository $repository,
        protected EntityManagerInterface $em,
        protected SpotDataTransformer $spotTransformer,
        protected SpotManager $spotManager,
        protected FeatureDataTransformer $featureTransformer,
        protected UserManager $userManager,
        protected Security $security,
        protected SpotRepository $spotRepository,
    ) {
    }

    public function handleCreate(SpotDTO $dto): SpotDTO
    {
        $spot = $this->spotTransformer->mapDTOtoEntity($dto);

        $spot = $this->spotManager->initSpotOwner($spot);

        $spot = $this->spotRepository->createSpot($spot);

        return $this->spotTransformer->mapEntityToDTO($spot);
    }

    public function handleGetFeatureCollection(): SpotFeatureCollectionDTO
    {
        $userId = $this->security->getUser()->getUserIdentifier();

        $spots = $this->em->getRepository(Spot::class)->findBy(['owner' => $userId]);

        return $this->featureTransformer->mapEntityToDTO($spots);
    }

    public function handleGet(int $id): SpotDTO
    {
        $test = $this->security->getUser()->getUserIdentifier();

        $spot = $this->em->getRepository(Spot::class)->findOneBy(['id' => $id]);

        // Ajouter une erreur

        // TODO : ajouter la vérification de l'identité du user !

        return $this->spotTransformer->mapEntityToDTO($spot);
    }
}

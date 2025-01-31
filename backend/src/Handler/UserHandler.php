<?php

namespace App\Handler;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Service\DataTransformer\UserDataTransformer;
use App\Service\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserHandler
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserDataTransformer $userTransformer,
        protected UserManager $userManager,
        protected JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function handleCreate(UserDTO $dto): UserDTO
    {
        // Check if user already exists
        $userRepository = $this->em->getRepository(User::class);
        if ($userRepository->findOneBy(['email' => $dto->email])) {
            throw new ConflictHttpException('User already exists with this email');
        }
        if ($userRepository->findOneBy(['pseudo' => $dto->pseudo])) {
            throw new ConflictHttpException('This pseudo already exists');
        }

        $user = $this->userTransformer->mapDTOToEntity($dto);

        $this->em->persist($user);
        $this->em->flush();

        $user->setToken($this->jwtManager->create($user));

        return $this->userTransformer->mapEntityToDTO($user);
    }
}

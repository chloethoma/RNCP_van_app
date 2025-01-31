<?php

namespace App\Handler;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Service\DataTransformer\UserDataTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserHandler
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected UserDataTransformer $transformer,
        protected JWTTokenManagerInterface $jwtManager,
        protected AuthenticationSuccessHandler $handler,
    ) {
    }

    public function createUser(UserDTO $dto): UserDTO
    {
        // Check if user already exists
        $userRepository = $this->em->getRepository(User::class);
        if ($userRepository->findOneBy(['email' => $dto->email])) {
            throw new ConflictHttpException('User already exists with this email');
        }

        if ($userRepository->findOneBy(['pseudo' => $dto->pseudo])) {
            throw new ConflictHttpException('This pseudo already exists');
        }

        $userEntity = $this->transformer->mapDTOToEntity($dto);

        $this->em->persist($userEntity);
        $this->em->flush();

        $userEntity->setToken($this->jwtManager->create($userEntity));

        $userDto = $this->transformer->mapEntityToDTO($userEntity);

        return $userDto;
    }
}

<?php

namespace App\Service\DataTransformer;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Service\Security\PasswordHasher;
use App\Service\Validator\Validator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;

class UserDataTransformer
{
    public function __construct(
        protected Validator $validator,
        protected PasswordHasher $passwordHasher,
    ) {
    }

    public function mapDTOToEntity(UserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setPseudo($dto->pseudo);
        $user->setEmailVerified(false);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTime());

        // Valider l'entité
        // $errors = $validator->validate($user);
        // if (count($errors) > 0) {
        //     return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        // }
        return $user;
    }

    public function mapEntityToDTO(User $entity): UserDTO
    {
        return new UserDTO(
            id: $entity->getId(),
            email: $entity->getEmail(),
            emailVerified: $entity->isEmailVerified(),
            password: $entity->getPassword(),
            pseudo: $entity->getPseudo(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
            picture: $entity->getPicture(),
            token: $entity->getToken()
        );
    }

    public function mapJWTUserToUser(JWTUser $jwtEntity): User
    {
        $user = new User();
        $user->setId((int) $jwtEntity->getUserIdentifier());

        return $user;
    }
}

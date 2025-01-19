<?php

namespace App\Service\DataTransformer;

use App\DTO\User\UserDTO;
use App\Entity\User;
use App\Service\Validator\Validator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataTransformer
{
    public function __construct(
        protected Validator $validator,
        protected UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function mapDTOToEntity(UserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setPseudo($dto->pseudo);
        $user->setEmailVerified(false);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTime());

        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);

        // Valider l'entité
        // $errors = $validator->validate($user);
        // if (count($errors) > 0) {
        //     return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        // }
        return $user;
    }

    public function mapEntityToDTO(User $user): UserDTO
    {
        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            email_verified: $user->isEmailVerified(),
            password: $user->getPassword(),
            pseudo: $user->getPseudo(),
            created_at: $user->getCreatedAt(),
            updated_at: $user->getUpdatedAt(),
            picture: $user->getPicture()
        );
    }
}

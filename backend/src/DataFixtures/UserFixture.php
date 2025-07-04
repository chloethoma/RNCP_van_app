<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public const TOTAL_USER = 20;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    private function createUser(ObjectManager $manager, string $email, string $pseudo, string $password, string $reference): void
    {
        $user = new User();
        $user->setEmail($email);
        $user->setEmailVerified(true);
        $user->setPseudo($pseudo);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTime());
        $user->setPicture(null);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $manager->persist($user);
        $this->addReference($reference, $user);
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < self::TOTAL_USER / 2; ++$i) {
            $email = "user{$i}@example.com";
            $pseudo = "User{$i}";
            $password = "password{$i}";
            $reference = "user_{$i}";

            $this->createUser($manager, $email, $pseudo, $password, $reference);
        }

        for ($i = self::TOTAL_USER / 2; $i <= self::TOTAL_USER; ++$i) {
            $email = "other_user{$i}@example.com";
            $pseudo = "OtherUser{$i}";
            $password = "password{$i}";
            $reference = "user_{$i}";

            $this->createUser($manager, $email, $pseudo, $password, $reference);
        }

        $manager->flush();
    }
}

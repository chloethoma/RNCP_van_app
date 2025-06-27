<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $userAlice = new User();
        $userAlice->setEmail('alice@example.com');
        $userAlice->setEmailVerified(true);
        $userAlice->setPseudo('Alice');
        $userAlice->setCreatedAt(new \DateTimeImmutable());
        $userAlice->setUpdatedAt(new \DateTime());
        $userAlice->setPicture(null);
        $userAlice->setPassword($this->passwordHasher->hashPassword($userAlice, 'password'));

        $manager->persist($userAlice);
        $this->addReference('user_alice', $userAlice);

        $userBob = new User();
        $userBob->setEmail('bob@example.com');
        $userBob->setEmailVerified(true);
        $userBob->setPseudo('Bob');
        $userBob->setCreatedAt(new \DateTimeImmutable());
        $userBob->setUpdatedAt(new \DateTime());
        $userBob->setPicture(null);
        $userBob->setPassword($this->passwordHasher->hashPassword($userBob, 'password'));

        $manager->persist($userBob);
        $this->addReference('user_bob', $userBob);

        $manager->flush();
    }
}

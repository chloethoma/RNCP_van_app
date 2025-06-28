<?php

namespace App\DataFixtures;

use App\Entity\Spot;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SpotFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userAlice = $this->getReference('user_alice', User::class);
        $userBob = $this->getReference('user_bob', User::class);

        $spot1 = new Spot();
        $spot1->setLatitude(45.0);
        $spot1->setLongitude(5.0);
        $spot1->setDescription('Spot de test Alice');
        $spot1->setOwner($userAlice);
        $spot1->setIsFavorite(true);
        $manager->persist($spot1);

        $spot2 = new Spot();
        $spot2->setLatitude(46.0);
        $spot2->setLongitude(4.0);
        $spot2->setDescription('Spot de test Bob');
        $spot2->setOwner($userBob);
        $spot2->setIsFavorite(false);
        $manager->persist($spot2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}

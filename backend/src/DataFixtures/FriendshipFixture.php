<?php

namespace App\DataFixtures;

use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FriendshipFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userAlice = $this->getReference('user_alice', User::class);
        $userBob = $this->getReference('user_bob', User::class);

        $friendship = new Friendship();
        $friendship->setRequester($userAlice);
        $friendship->setReceiver($userBob);
        $friendship->setConfirmed(true);
        $manager->persist($friendship);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}

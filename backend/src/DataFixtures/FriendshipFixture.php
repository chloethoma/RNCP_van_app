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
        $totalUsers = UserFixture::TOTAL_USER;

        for ($userId = 1; $userId <= $totalUsers; ++$userId) {
            $user = $this->getReference("user_{$userId}", User::class);

            $usedIds = [];

            $friendshipAdded = 0;
            while ($friendshipAdded < 3) {
                $randomId = random_int(1, $totalUsers);

                if ($randomId !== $userId && !in_array($randomId, $usedIds)) {
                    $friend = $this->getReference("user_{$randomId}", User::class);

                    $friendship = new Friendship();
                    $friendship->setRequester($user);
                    $friendship->setReceiver($friend);
                    $friendship->setConfirmed(true);
                    $manager->persist($friendship);

                    $usedIds[] = $randomId;
                    ++$friendshipAdded;
                }
            }

            $pendingFriendshipAdded = 0;
            while ($pendingFriendshipAdded < 2) {
                $randomId = random_int(1, $totalUsers);

                if ($randomId !== $userId && !in_array($randomId, $usedIds)) {
                    $friend = $this->getReference("user_{$randomId}", User::class);

                    $friendship = new Friendship();
                    $friendship->setRequester($user);
                    $friendship->setReceiver($friend);
                    $friendship->setConfirmed(false);
                    $manager->persist($friendship);

                    $usedIds[] = $randomId;
                }
                ++$pendingFriendshipAdded;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}

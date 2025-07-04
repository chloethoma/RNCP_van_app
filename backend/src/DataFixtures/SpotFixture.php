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
        for ($i = 1; $i <= UserFixture::TOTAL_USER; ++$i) {
            /** @var User $user */
            $user = $this->getReference("user_{$i}", User::class);

            for ($j = 1; $j <= 5; ++$j) {
                $spot = new Spot();
                $coords = $this->getRandomFrenchCoordinates();
                $spot->setLatitude($coords['lat']);
                $spot->setLongitude($coords['lng']);
                $spot->setDescription("Spot {$j} de User{$i}");
                $spot->setOwner($user);
                $spot->setIsFavorite(($j % 2) === 0);
                $manager->persist($spot);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }

    /**
     * Returns random coordinates within a bounding box around France.
     *
     * @return array{lat: float, lng: float}
     */
    private function getRandomFrenchCoordinates(): array
    {
        $minLat = 42.5;
        $maxLat = 50.8;
        $minLng = -1.5;
        $maxLng = 8.2;

        return [
            'lat' => mt_rand((int) ($minLat * 10000), (int) ($maxLat * 10000)) / 10000,
            'lng' => mt_rand((int) ($minLng * 10000), (int) ($maxLng * 10000)) / 10000,
        ];
    }
}

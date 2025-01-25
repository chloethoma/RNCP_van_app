<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotGeometry
{
    public const TYPE = 'Point';

    #[Assert\IdenticalTo(self::TYPE)]
    public string $type = self::TYPE;

    #[Assert\Type('array')]
    #[Assert\Count(min: 2, max: 2)]
    #[Assert\All([
        new Assert\Type(type: 'float', message: 'this must be float'),
    ])]
    public array $coordinates;

    public function __construct(
        float $longitude,
        float $latitude,
    ) {
        $this->coordinates = [$longitude, $latitude];
    }
}

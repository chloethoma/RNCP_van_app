<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotFeatureCollectionDTO
{
    public const TYPE = 'FeatureCollection';

    #[Assert\IdenticalTo(self::TYPE)]
    public string $type = self::TYPE;

    #[Assert\Valid]
    #[Assert\All([
        new Assert\Type(SpotFeatureDTO::class),
    ])]
    /**
     * @var SpotFeatureDTO[]
     */
    public array $features = [];

    public function __construct(array $features)
    {
        $this->features = $features;
    }
}

<?php 

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotFeatureCollectionOutput
{
    const TYPE = 'FeatureCollection'; 

    #[Assert\IdenticalTo(self::TYPE)]
    public string $type = self::TYPE;

    #[Assert\Valid]
    #[Assert\All([
        new Assert\Type(SpotFeatureOutput::class)
    ])]
    /**
     * @var SpotFeatureOutput[]
     */
    public array $features = [];

    public function __construct(array $features)
    {
        $this->features = $features;
    }
}
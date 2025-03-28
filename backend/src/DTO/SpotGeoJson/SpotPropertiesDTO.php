<?php

namespace App\DTO\SpotGeoJson;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotPropertiesDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public int $spotId,

        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public int $ownerId,
    ) {
    }
}

<?php

namespace App\DTO\Feature;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotPropertiesDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public int $id,
    ) {
    }
}

<?php

namespace App\DTO\Spot;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $id,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        #[Assert\Type(type: 'float', groups: ['read', 'create', 'update'])]
        #[Assert\Range(min: -90, max: 90, groups: ['read', 'create', 'update'])]
        public readonly float $latitude,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        #[Assert\Type(type: 'float', groups: ['read', 'create', 'update'])]
        #[Assert\Range(min: -180, max: 180, groups: ['read', 'create', 'update'])]
        public readonly float $longitude,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotBlank(groups: ['read', 'create', 'update'])]
        public readonly ?string $description,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\Type(type: 'boolean', groups: ['read', 'create', 'update'])]
        public readonly bool $isFavorite = false,

        #[Groups(['read', 'update'])]
        #[Assert\NotNull(groups: ['read', 'update'])]
        public readonly ?int $userId = null,
    ) {
    }
}

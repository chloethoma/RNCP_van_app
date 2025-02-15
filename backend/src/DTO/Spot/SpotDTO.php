<?php

namespace App\DTO\Spot;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotDTO
{
    public function __construct(
        #[Groups(['read', 'update'])]
        #[Assert\NotNull(groups: ['read', 'update'])]
        public readonly ?int $id,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        public readonly float $latitude,

        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
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

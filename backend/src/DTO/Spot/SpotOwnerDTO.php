<?php

namespace App\DTO\Spot;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotOwnerDTO
{
    public function __construct(
        #[Groups(['read'])]
        #[Assert\NotNull(groups: ['read'])]
        public readonly ?int $id,

        #[Groups(['read'])]
        #[Assert\NotBlank(groups: ['read'])]
        public readonly ?string $pseudo,

        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $picture,
    ) {
    }
}

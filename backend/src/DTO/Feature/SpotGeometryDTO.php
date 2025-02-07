<?php

namespace App\DTO\Feature;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SpotGeometryDTO
{
    public const TYPE = 'Point';

    public function __construct(
        #[Groups(['read'])]
        #[Assert\Type(type: 'array', groups: ['read'])]
        #[Assert\Count(min: 2, max: 2, groups: ['read'])]
        #[Assert\All([
            new Assert\Type(type: 'float', message: 'this must be float', groups: ['read']),
        ])]
        public array $coordinates,

        #[Groups(['read'])]
        #[Assert\IdenticalTo(value: self::TYPE, groups: ['read'])]
        public ?string $type = self::TYPE,
    ) {
    }
}

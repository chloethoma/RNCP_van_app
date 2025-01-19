<?php

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotProperties
{
    #[Assert\Type('int')]
    #[Assert\NotNull()]
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

<?php 

namespace App\DTO\Feature;

use Symfony\Component\Validator\Constraints as Assert;

class SpotPropertiesOutput
{
    #[Assert\Type('int')]
    #[Assert\NotNull()]
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
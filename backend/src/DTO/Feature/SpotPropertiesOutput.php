<?php 

namespace App\DTO\Feature;

class SpotPropertiesOutput
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
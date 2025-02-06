<?php

namespace App\Entity;

class SpotCollection extends \ArrayObject
{
    /**
     * @return \Iterator<int, Spot>
     */
    public function getIterator(): \Iterator
    {
        return parent::getIterator();
    }
}

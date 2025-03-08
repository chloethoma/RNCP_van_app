<?php

namespace App\Entity;

class UserCollection extends \ArrayObject
{
    /**
     * @return \Iterator<int, User>
     */
    public function getIterator(): \Iterator
    {
        return parent::getIterator();
    }
}

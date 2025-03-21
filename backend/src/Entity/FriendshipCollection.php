<?php

namespace App\Entity;

class FriendshipCollection extends \ArrayObject
{
    /**
     * @return \Iterator<int, Friendship>
     */
    public function getIterator(): \Iterator
    {
        return parent::getIterator();
    }
}

<?php

namespace App\Services\Exceptions\Friendship;

use App\Services\Exceptions\AbstractDomainException;

class FriendshipBadRequestException extends AbstractDomainException
{
    protected $message = 'Some params sent are not correct or not formatted correctly';
    protected $code = 3000;
}

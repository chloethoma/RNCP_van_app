<?php

namespace App\Services\Exceptions\Friendship;

use App\Services\Exceptions\AbstractDomainException;

class FriendshipConflictException extends AbstractDomainException
{
    protected $message = 'Friendship already exists';
    protected $code = 3009;
}

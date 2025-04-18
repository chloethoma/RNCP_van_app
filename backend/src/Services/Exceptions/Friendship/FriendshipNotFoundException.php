<?php

namespace App\Services\Exceptions\Friendship;

use App\Services\Exceptions\AbstractDomainException;

class FriendshipNotFoundException extends AbstractDomainException
{
    protected $message = 'Friendship not found';
    protected $code = 3004;
}

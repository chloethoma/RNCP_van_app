<?php

namespace App\Services\Exceptions\User;

use App\Services\Exceptions\AbstractDomainException;

class UserNotFoundException extends AbstractDomainException
{
    protected $message = 'User not found';
    protected $code = 1004;
}

<?php

namespace App\Services\Exceptions\User;

use App\Services\Exceptions\AbstractDomainException;

class UnauthenticatedUserException extends AbstractDomainException
{
    protected $message = 'No authenticated user found in JWT';
    protected $code = 1001;
}

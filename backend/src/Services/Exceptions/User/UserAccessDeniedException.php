<?php

namespace App\Services\Exceptions\User;

use App\Services\Exceptions\AbstractDomainException;

class UserAccessDeniedException extends AbstractDomainException
{
    protected $message = 'You are not authorized to perform this action';
    protected $code = 1003;
}

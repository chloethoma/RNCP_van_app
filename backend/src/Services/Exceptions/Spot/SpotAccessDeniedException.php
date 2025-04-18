<?php

namespace App\Services\Exceptions\Spot;

use App\Services\Exceptions\AbstractDomainException;

class SpotAccessDeniedException extends AbstractDomainException
{
    protected $message = 'You are not authorized to perform this action';
    protected $code = 2003;
}

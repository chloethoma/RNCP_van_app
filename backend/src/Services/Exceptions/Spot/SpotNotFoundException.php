<?php

namespace App\Services\Exceptions\Spot;

use App\Services\Exceptions\AbstractDomainException;

class SpotNotFoundException extends AbstractDomainException
{
    protected $message = 'Spot not found';
    protected $code = 2004;
}

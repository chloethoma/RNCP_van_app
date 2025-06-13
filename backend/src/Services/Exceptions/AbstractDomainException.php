<?php

namespace App\Services\Exceptions;

abstract class AbstractDomainException extends \RuntimeException
{
    public function __construct(?string $message = null, ?\Throwable $previous = null)
    {
        parent::__construct($message ?? $this->message, $this->code, $previous);
    }
}

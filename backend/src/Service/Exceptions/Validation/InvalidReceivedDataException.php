<?php

namespace App\Service\Exceptions\Validation;

class InvalidReceivedDataException extends \Exception
{
    protected $message = 'Errors found in received data in %s';
    protected $details = [];
    public static $staticCode = 1001;

    public function __construct(string $class)
    {
        $this->message = sprintf($this->message, $class);
        parent::__construct($this->message, static::$staticCode);
    }

    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}

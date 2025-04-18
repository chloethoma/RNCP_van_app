<?php

namespace App\Services\Exceptions\User;

use App\Services\Exceptions\AbstractDomainException;

class UserConflictException extends AbstractDomainException
{
    protected $message = 'User already exists';
    protected $code = 1009;

    private array $details;

    public function __construct(array $details = [], ?\Throwable $previous = null)
    {
        $this->details = $details;
        parent::__construct($this->message, $previous);
    }

    public function getDetails(): array
    {
        return array_map(function (string $field) {
            return [
                'target' => $field,
                'message' => 'User already exists with this '.$field,
            ];
        }, $this->details);
    }
}

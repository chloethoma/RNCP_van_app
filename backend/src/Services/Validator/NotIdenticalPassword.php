<?php

namespace App\Services\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NotIdenticalPassword extends Constraint
{
    public string $message = 'The new password must be different from the current one';
}

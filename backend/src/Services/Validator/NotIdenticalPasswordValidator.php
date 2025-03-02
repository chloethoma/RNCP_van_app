<?php

namespace App\Services\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotIdenticalPasswordValidator extends ConstraintValidator
{
    public function validate($newPassword, Constraint $constraint): void
    {
        if (!$constraint instanceof NotIdenticalPassword) {
            throw new UnexpectedTypeException($constraint, NotIdenticalPassword::class);
        }

        $dto = $this->context->getObject();
        $oldPassword = $dto->oldPassword;

        if ($oldPassword === $newPassword) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

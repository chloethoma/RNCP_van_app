<?php

namespace App\Services\Validator;

use App\Services\Exceptions\Validation\InvalidReceivedDataException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws InvalidReceivedDataException
     */
    public function validate(object $dto, string $class, array $groups = []): void
    {
        $errors = $this->validateEngine($dto, $groups);
        if (!empty($errors)) {
            throw (new InvalidReceivedDataException($class))->setDetails($errors);
        }
    }

    private function validateEngine($dto, array $groups): array
    {
        $errors = [];

        $violations = $this->validator->validate($dto, null, $groups);
        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[$propertyPath][] = $violation->getMessage();
            }
        }

        return $errors;
    }
}

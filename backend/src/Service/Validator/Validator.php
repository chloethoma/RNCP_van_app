<?php

namespace App\Service\Validator;

use App\Service\Exceptions\Validation\InvalidReceivedDataException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @throws InvalidReceivedDataException
     */
    public function validate(object $dto, string $class): void
    {
        $errors = $this->validateEngine($dto);
        if (!empty($errors)) {
            throw (new InvalidReceivedDataException($class))->setDetails($errors);
        }
    }

    private function validateEngine($dto): array
    {
        $errors = [];

        $violations = $this->validator->validate($dto);
        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[$propertyPath][] = $violation->getMessage();
            }
        }

        return $errors;
    }
}

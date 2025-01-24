<?php

namespace App\Service\Exceptions\Validation;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class RequestValidationErrorEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $rootCause = $event->getThrowable()->getPrevious();

        $isJsonFormatError = $rootCause instanceof NotEncodableValueException;
        $isValidationEvent = $rootCause instanceof ValidationFailedException;

        if (!$isJsonFormatError && !$isValidationEvent) {
            return;
        }

        if ($isJsonFormatError) {
            /**
             * @var NotEncodableValueException $rootCause
             */
            $response = $this->getResponseDecorator(
                'InvalidRequest',
                $rootCause->getMessage(),
                null
            );
        }

        if ($isValidationEvent) {
            /**
             * @var ValidationFailedException $rootCause
             */
            $details = [];

            foreach ($rootCause->getViolations() as $violation) {
                $details[] = $this->getResponseDetailsDecorator(
                    $this->getCodeFromViolation($violation),
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            $target = (new \ReflectionClass($rootCause->getValue()))->getShortName();
            $response = $this->getResponseDecorator(
                'InvalidRequest',
                'Errors found in submitted data',
                $target,
                $details
            );
        }

        $event->setResponse(new JsonResponse($response, Response::HTTP_BAD_REQUEST));
    }

    private function getResponseDecorator($code, $message, $target, $details = null): array
    {
        return [
            'error' => [
                'code' => $code,
                'message' => $message,
                'target' => $target,
                'details' => $details,
            ],
        ];
    }

    private function getResponseDetailsDecorator($code, $target, $message): array
    {
        return [
            'code' => $code,
            'target' => $target,
            'message' => $message,
        ];
    }

    private function getCodeFromViolation($violation): string
    {
        $constraint = $violation->getConstraint();

        if (null === $constraint) {
            /**
             * Conditions based on observations made
             * by dumping the $violation object.
             */
            $parameters = $violation->getParameters();

            if (isset($parameters['hint'])) {
                return 'MissingValue';
            }

            return 'TypeError';
        }

        return (new \ReflectionClass($constraint))->getShortName();
    }
}

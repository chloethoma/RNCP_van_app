<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationFailureListener
{
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $this->handleJwtEvent($event, 'Unauthorized', 'Bad credentials', Response::HTTP_UNAUTHORIZED);
    }

    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $this->handleJwtEvent($event, 'Access denied', 'Invalid JWT token', Response::HTTP_FORBIDDEN);
    }

    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $this->handleJwtEvent($event, 'Access denied', 'Missing JWT token', Response::HTTP_FORBIDDEN);
    }

    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $this->handleJwtEvent($event, 'Access denied', 'Token expired, please renew it.', Response::HTTP_FORBIDDEN);
    }

    private function handleJwtEvent($event, string $code, string $message, int $statusCode): void
    {
        $response = new JsonResponse([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $statusCode);

        $event->setResponse($response);
    }
}

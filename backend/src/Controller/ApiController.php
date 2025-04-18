<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    public function logException(string $location, \Throwable $th): self
    {
        $this->logger->error($location, [
            'code' => $th->getCode(),
            'message' => $th->getMessage(),
            'trace' => $th->getTrace(),
        ]);

        return $this;
    }

    protected function handleException(\Throwable $exception, string $forTarget): JsonResponse
    {
        $this->logException(__METHOD__, $exception);

        return $this->serveServerErrorResponse('Oops ! Something went wrong.', $forTarget);
    }

    public function serveOkResponse(object $content, array $headers = [], array $groups = []): JsonResponse
    {
        $context = $this->buildContext($groups);

        return $this->json($content, Response::HTTP_OK, $headers, $context);
    }

    public function serveCreatedResponse(object $content, string $location, array $groups = []): JsonResponse
    {
        $headers = ['Location' => $location];
        $context = $this->buildContext($groups);

        return $this->json($content, Response::HTTP_CREATED, $headers, $context);
    }

    public function serveNoContentResponse(): JsonResponse
    {
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    public function serveNotFoundResponse(string $message, string $target): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'NotFound',
                    'message' => $message,
                    'target' => $target,
                ],
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    public function serveServerErrorResponse(string $message, string $target): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'ServerError',
                    'message' => $message,
                    'target' => $target,
                ],
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function serveConflictResponse(string $message, string $target, ?array $details = null): JsonResponse
    {
        $responseData = [
            'error' => [
                'code' => 'Conflict',
                'message' => $message,
                'target' => $target,
            ],
        ];

        if (null !== $details) {
            $responseData['error']['details'] = $details;
        }

        return $this->json($responseData, Response::HTTP_CONFLICT
        );
    }

    public function serveUnauthorizedResponse(string $message, string $target): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'Unauthorized',
                    'message' => $message,
                    'target' => $target,
                ],
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function serveAccessDeniedResponse(string $message, string $target): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'AccessDenied',
                    'message' => $message,
                    'target' => $target,
                ],
            ],
            Response::HTTP_FORBIDDEN
        );
    }

    public function serveBadRequestResponse(string $message, string $target): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'Bad Request',
                    'message' => $message,
                    'target' => $target,
                ],
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    private function buildContext(?array $groups = null): array
    {
        return $groups ? ['groups' => $groups] : [];
    }
}

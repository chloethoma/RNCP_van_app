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
        // if ($exception instanceof ) {
        //     $response = $this->serveInvalidRequestResponse(
        //         $exception->getMessage(),
        //         $forTarget,
        //         $exception->getDetails()
        //     );
        // } else {
        $this->logException(__METHOD__, $exception);
        $response = $this->serveServerErrorResponse('Oops ! Something went wrong.', $forTarget);
        // }

        return $response;
    }

    public function serveOkResponse(object $content, array $headers = []): JsonResponse
    {
        return $this->json($content, Response::HTTP_OK, $headers);
    }

    public function serveCreatedResponse(object $content, string $location): JsonResponse
    {
        $headers = ['Location' => $location];

        return $this->json($content, Response::HTTP_CREATED, $headers);
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

    public function serveInvalidRequestResponse(string $message, string $target, array $details): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'InvalidRequest',
                    'message' => $message,
                    'target' => $target,
                    'details' => $details,
                ],
            ],
            Response::HTTP_BAD_REQUEST
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

    public function serveConflictResponse(string $message, string $target): JsonResponse
    {
        return $this->json(
            [
                'error' => [
                    'code' => 'Conflict',
                    'message' => $message,
                    'target' => $target,
                ],
            ],
            Response::HTTP_CONFLICT
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
}

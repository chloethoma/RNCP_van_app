<?php

namespace App\Controller;

use App\DTO\User\UserDTO;
use App\Handler\UserHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends ApiController
{
    public const TARGET = 'Registration controller';

    public function __construct(
        LoggerInterface $logger,
        protected UserHandler $userHandler,
    ) {
        parent::__construct($logger);
    }

    #[Route('/register', name: 'register', methods: ['POST'], format: 'json')]
    public function createUser(
        #[MapRequestPayload(validationGroups: ['create'], serializationContext: ['groups' => ['create']])] UserDTO $dto,
    ): JsonResponse {
        try {
            $newUser = $this->userHandler->handleCreate($dto);
            $response = $this->serveCreatedResponse($newUser, self::TARGET, groups: ['read']);
        } catch (HttpException $e) {
            $response = $this->serveConflictResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

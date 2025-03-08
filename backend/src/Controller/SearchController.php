<?php

namespace App\Controller;

use App\Handler\UserHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends ApiController
{
    public const TARGET = 'Search controller';

    public function __construct(
        LoggerInterface $logger,
        protected UserHandler $handler,
    ) {
        parent::__construct($logger);
    }

    #[Route(
        path: '/api/search/user',
        name: 'search_users',
        methods: ['GET'],
        format: 'json')]
    public function searchUsers(
        #[MapQueryParameter()] string $pseudo,
    ): JsonResponse {
        try {
            $userList = $this->handler->handleSearchUser($pseudo);

            $response = $this->serveOkResponse($userList, groups: ['search_read']);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

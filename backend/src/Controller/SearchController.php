<?php

namespace App\Controller;

use App\DTO\User\UserDTO;
use App\Enum\ErrorMessage;
use App\Handler\UserHandler;
use App\Services\Exceptions\User\UnauthenticatedUserException;
use App\Services\Exceptions\User\UserNotFoundException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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

    /**
     * Search user in database.
     */
    #[Route(
        path: '/api/search/users',
        name: 'search_users',
        methods: ['GET'],
        format: 'json'
    )]
    #[OA\Tag(name: 'Search users')]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'Users list matching with the search query',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: UserDTO::class, groups: ['search_read']))
        )
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_UNAUTHORIZED,
        description: ErrorMessage::USER_UNAUTHENTICATED->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_NOT_FOUND,
        description: ErrorMessage::USER_NOT_FOUND->value,
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        description: ErrorMessage::INTERNAL_SERVER_ERROR->value,
    )]
    public function searchUsers(
        #[MapQueryParameter()] string $pseudo,
    ): JsonResponse {
        try {
            $userList = $this->handler->handleSearchUser($pseudo);

            $response = $this->serveOkResponse($userList, groups: ['search_read']);
        } catch (UnauthenticatedUserException $e) {
            $response = $this->serveUnauthorizedResponse($e->getMessage(), self::TARGET);
        } catch (UserNotFoundException $e) {
            $response = $this->serveNotFoundResponse($e->getMessage(), self::TARGET);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, self::TARGET);
        }

        return $response;
    }
}

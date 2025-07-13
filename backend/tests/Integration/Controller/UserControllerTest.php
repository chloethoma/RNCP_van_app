<?php

namespace App\Tests\Integration\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private const EXINSTING_USER_EMAIL = 'user1@example.com';
    private const EXINSTING_USER_PSEUDO = 'User1';
    private const OTHER_EXINSTING_USER_EMAIL = 'user2@example.com';

    private KernelBrowser $client;
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $jwtManager;

    public function setup(): void
    {
        $this->client = static::createClient();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);
    }

    // ----------------------------------------------------------------------------
    // Create User Tests
    // ----------------------------------------------------------------------------
    public function testCreateUserReturnsSuccess(): void
    {
        $payload = [
            'email' => 'test@example.com',
            'password' => 'securePassword1234!!!',
            'pseudo' => 'testUser',
        ];

        $this->requestMethod('POST', '/register', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = $this->decodeResponse();

        $this->assertEqualsCanonicalizing($this->expectedUserDTOKeys(), array_keys($responseData));
        $this->assertEquals('test@example.com', $responseData['email']);
        $this->assertArrayNotHasKey('password', $responseData);
    }

    public static function createUserBadPayloadDataProvider(): array
    {
        return [
            'Missing email' => [[
                'password' => 'securePassword1234!!!',
                'pseudo' => 'valid_pseudo',
            ]],
            'Empty email' => [[
                'email' => '',
                'password' => 'securePassword1234!!!',
                'pseudo' => 'valid_pseudo',
            ]],
            'Invalid email format' => [[
                'email' => 'not-an-email',
                'password' => 'securePassword1234!!!',
                'pseudo' => 'valid_pseudo',
            ]],
            'Missing password' => [[
                'email' => 'test@example.com',
                'pseudo' => 'valid_pseudo',
            ]],
            'Empty password' => [[
                'email' => 'test@example.com',
                'password' => '',
                'pseudo' => 'valid_pseudo',
            ]],
            'Missing pseudo' => [[
                'email' => 'test@example.com',
                'password' => 'securePassword1234!!!',
            ]],
            'Pseudo with invalid characters' => [[
                'email' => 'test@example.com',
                'password' => 'securePassword1234!!!',
                'pseudo' => 'invalid pseudo!',
            ]],
        ];
    }

    #[Attributes\DataProvider('createUserBadPayloadDataProvider')]
    public function testCreateUserReturnsBadRequestException(array $payload): void
    {
        $this->requestMethod('POST', '/register', $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateUserReturnsConflictException(): void
    {
        $payload = [
            'email' => self::EXINSTING_USER_EMAIL,
            'password' => 'securePassword1234!!!',
            'pseudo' => self::EXINSTING_USER_PSEUDO,
        ];

        $this->requestMethod('POST', '/register', $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    // ----------------------------------------------------------------------------
    // Get User Identity Tests
    // ----------------------------------------------------------------------------
    public function testGetUserIdentityReturnsSuccess(): void
    {
        $this->authenticateUserWithJwt();
        $this->requestMethod('GET', '/api/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $this->decodeResponse();

        $this->assertEqualsCanonicalizing($this->expectedUserDTOKeys(), array_keys($responseData));
        $this->assertEquals(self::EXINSTING_USER_EMAIL, $responseData['email']);
        $this->assertArrayNotHasKey('password', $responseData);
    }

    // ----------------------------------------------------------------------------
    // Update User Tests
    // ----------------------------------------------------------------------------
    public function testUpdateUserReturnsSuccess(): void
    {
        $payload = [
            'email' => 'updated_email@example.com',
            'pseudo' => 'updatedPseudo',
            'picture' => null,
            'emailVerified' => false,
        ];

        $this->authenticateUserWithJwt();
        $this->requestMethod('PUT', '/api/users', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $this->decodeResponse();

        $this->assertEqualsCanonicalizing($this->expectedUserDTOKeys(), array_keys($responseData));
        $this->assertEquals('updated_email@example.com', $responseData['email']);
        $this->assertEquals('updatedPseudo', $responseData['pseudo']);
    }

    public static function updateUserPayloadDataProvider(): array
    {
        return [
            'Empty email' => [[
                'email' => '',
                'pseudo' => 'validPseudo',
                'picture' => null,
                'emailVerified' => false,
            ]],
            'Invalid email format' => [[
                'email' => 'invalid-email',
                'pseudo' => 'validPseudo',
                'picture' => null,
                'emailVerified' => false,
            ]],
            'Too short pseudo' => [[
                'email' => 'valid@example.com',
                'pseudo' => 'ab',
                'picture' => null,
                'emailVerified' => false,
            ]],
            'Pseudo with invalid characters' => [[
                'email' => 'valid@example.com',
                'pseudo' => 'invalid pseudo!',
                'picture' => null,
                'emailVerified' => false,
            ]],
            'emailVerified as string instead of bool' => [[
                'email' => 'valid@example.com',
                'pseudo' => 'validPseudo',
                'picture' => null,
                'emailVerified' => 'yes',
            ]],
        ];
    }

    #[Attributes\DataProvider('updateUserPayloadDataProvider')]
    public function testUpdateUserReturnsBadRequestException(array $payload): void
    {
        $this->authenticateUserWithJwt();
        $this->requestMethod('PUT', '/api/users', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateUserReturnsConflictException(): void
    {
        $payload = [
            'email' => self::OTHER_EXINSTING_USER_EMAIL,
            'pseudo' => 'updatedPseudo',
            'picture' => null,
            'emailVerified' => false,
        ];

        $this->authenticateUserWithJwt();
        $this->requestMethod('PUT', '/api/users', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    // ----------------------------------------------------------------------------
    // Update User Password Tests
    // ----------------------------------------------------------------------------
    public function testUpdateUserPasswordReturnsVoidSuccess(): void
    {
        $payload = [
            'currentPassword' => 'password1',
            'newPassword' => 'newSecurePassword123!!',
        ];

        $this->authenticateUserWithJwt();
        $this->requestMethod('PATCH', '/api/users', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertEmpty($this->client->getResponse()->getContent());
    }

    public static function updateUserPasswordBadPayloadDataProvider(): array
    {
        return [
            'Missing currentPassword' => [[
                'newPassword' => 'NewSecurePassword123!',
            ]],
            'Empty currentPassword' => [[
                'currentPassword' => '',
                'newPassword' => 'NewSecurePassword123!',
            ]],
            'Missing newPassword' => [[
                'currentPassword' => 'password1',
            ]],
            'Empty newPassword' => [[
                'currentPassword' => 'password1',
                'newPassword' => '',
            ]],
            'newPassword identical to currentPassword' => [[
                'currentPassword' => 'password1',
                'newPassword' => 'password1',
            ]],
            'Weak newPassword (too short)' => [[
                'currentPassword' => 'password1',
                'newPassword' => '123',
            ]],
        ];
    }

    #[Attributes\DataProvider('updateUserPasswordBadPayloadDataProvider')]
    public function testUpdateUserPasswordReturnsBadRequestException(array $payload): void
    {
        $this->authenticateUserWithJwt();
        $this->requestMethod('PATCH', '/api/users', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateUserPasswordReturnsAccessDeniedException(): void
    {
        $payload = [
            'currentPassword' => 'WrongPassword',
            'newPassword' => 'newSecurePassword123!!',
        ];

        $this->authenticateUserWithJwt();
        $this->requestMethod('PATCH', '/api/users', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // ----------------------------------------------------------------------------
    // Delete User Tests
    // ----------------------------------------------------------------------------
    public function testDeleteUserReturnsVoidSuccess(): void
    {
        $this->authenticateUserWithJwt();
        $this->requestMethod('DELETE', '/api/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertEmpty($this->client->getResponse()->getContent());
    }

    // ----------------------------------------------------------------------------
    // Get User Summary Tests
    // ----------------------------------------------------------------------------
    public function testGetUSerSummaryReturnsSuccess(): void
    {
        $this->authenticateUserWithJwt();
        $this->requestMethod('GET', '/api/users/summary');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $this->decodeResponse();

        $this->assertEqualsCanonicalizing($this->expectedUserSummaryDTOKeys(), array_keys($responseData));
        $this->assertIsInt($responseData['friendsNumber']);
        $this->assertIsInt($responseData['spotsNumber']);
    }

    // ----------------------------------------------------------------------------
    // Unauthorized Exception Tests
    // ----------------------------------------------------------------------------
    public static function userMethodDataProvider()
    {
        $baseuUri = '/api/users';

        return [
            'Get User Identity' => ['GET', $baseuUri],
            'Update User' => ['PUT', $baseuUri],
            'Update User Password' => ['PATCH', $baseuUri],
            'Delete User' => ['DELETE', $baseuUri],
            'Get User Summary' => ['GET', $baseuUri.'/summary'],
        ];
    }

    #[Attributes\DataProvider('userMethodDataProvider')]
    public function testAllMethodsUnAuthorizedException(string $method, string $uri): void
    {
        // No login, no JWT in headers
        $this->requestMethod($method, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // ----------------------------------------------------------------------------
    // Private functions
    // ----------------------------------------------------------------------------
    private function requestMethod(string $method, string $uri, array $data = [], array $headers = []): void
    {
        $this->client->request(
            $method,
            $uri,
            server: array_merge(['CONTENT_TYPE' => 'application/json'], $headers),
            content: json_encode($data)
        );
    }

    private function decodeResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    private function authenticateUserWithJwt(): void
    {
        $user = $this->userRepository->findByEmail(self::EXINSTING_USER_EMAIL);
        $token = $this->jwtManager->create($user);
        $this->client->setServerParameter('HTTP_Authorization', 'Bearer '.$token);
    }

    private function expectedUserDTOKeys(): array
    {
        return [
            'id',
            'email',
            'pseudo',
            'picture',
            'token',
            'createdAt',
            'updatedAt',
            'emailVerified',
        ];
    }

    private function expectedUserSummaryDTOKeys(): array
    {
        return [
            'friendsNumber',
            'spotsNumber',
        ];
    }
}

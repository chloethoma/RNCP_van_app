<?php

namespace App\Tests\Integration\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SpotControllerTest extends WebTestCase
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
    // Create Spot Tests
    // ----------------------------------------------------------------------------
    public function testCreateSpotReturnsCreated(): void
    {
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'test',
        ];

        $this->authenticatePrincipalUserWithJwt();
        $this->requestMethod('POST', '/api/spots', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = $this->decodeResponse();

        $this->assertEqualsCanonicalizing($this->expectedSpotDTOKeys(), array_keys($responseData));
        $this->assertEquals('test', $responseData['description']);
        $this->assertFalse($responseData['isFavorite']);
    }

    public static function createSpotBadPayloadDataProvider(): array
    {
        return [
            'Missing latitude' => [[
                'longitude' => 5.685141,
                'description' => 'test spot',
            ]],
            'Missing longitude' => [[
                'latitude' => 45.421539,
                'description' => 'test spot',
            ]],
            'Missing description' => [[
                'latitude' => 45.421539,
                'longitude' => 5.685141,
            ]],
            'Blank description' => [[
                'latitude' => 45.421539,
                'longitude' => 5.685141,
                'description' => '',
            ]],
        ];
    }

    #[Attributes\DataProvider('createSpotBadPayloadDataProvider')]
    public function testCreateSpotReturnsBadRequestException(array $payload): void
    {
        $this->authenticatePrincipalUserWithJwt();
        $this->requestMethod('POST', '/api/spots', $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    // ----------------------------------------------------------------------------
    // Get Spot List Tests
    // ----------------------------------------------------------------------------
    public function testGetSpotsReturnsSuccess(): void
    {
        $this->authenticatePrincipalUserWithJwt();
        $this->requestMethod('GET', '/api/spots');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $this->decodeResponse();

        $expectedGeoJson = $this->expectedSpotCollectionDTO();

        $this->assertArrayHasKey('type', $responseData);
        $this->assertEquals($expectedGeoJson['type'], $responseData['type']);

        $this->assertArrayHasKey('features', $responseData);
        $this->assertIsArray($responseData['features']);
        $this->assertNotEmpty($responseData['features']);

        foreach ($responseData['features'] as $feature) {
            $this->assertEquals('Feature', $feature['type']);
            $this->assertEquals('Point', $feature['geometry']['type']);
            $this->assertIsArray($feature['geometry']['coordinates']);

            $this->assertArrayHasKey('spotId', $feature['properties']);
            $this->assertArrayHasKey('ownerId', $feature['properties']);
        }
    }

    // ----------------------------------------------------------------------------
    // Get Spot By ID Tests
    // ----------------------------------------------------------------------------
    public function testGetSpotReturnsSuccess(): void
    {
        $this->authenticatePrincipalUserWithJwt();

        // Create Spot for the test
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'spot to test GET',
        ];
        $this->requestMethod('POST', '/api/spots', $payload);
        $responseData = $this->decodeResponse();
        $spotId = $responseData['id'];

        // Call getSpot method
        $this->requestMethod('GET', '/api/spots/'.$spotId);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $spotResponse = $this->decodeResponse();
        $this->assertEquals($spotId, $spotResponse['id']);
        $this->assertEquals('spot to test GET', $spotResponse['description']);
    }

    public function testGetSpotReturnsAccessDeniedException(): void
    {
        $this->authenticateUserWithJwt(self::OTHER_EXINSTING_USER_EMAIL);

        // Create Spot for the test for other user
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'spot to test GET',
        ];
        $this->requestMethod('POST', '/api/spots', $payload);
        $responseData = $this->decodeResponse();
        $spotId = $responseData['id'];

        // Now, authenticate principal user and call getSpot with id of spot create juste before
        $this->authenticatePrincipalUserWithJwt();

        $this->requestMethod('GET', '/api/spots/'.$spotId);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // ----------------------------------------------------------------------------
    // Update Spot Tests
    // ----------------------------------------------------------------------------
    public function testUpdateSpotReturnsSuccess(): void
    {
        $this->authenticatePrincipalUserWithJwt();

        // Create Spot
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'test create spot',
        ];
        $this->requestMethod('POST', '/api/spots', $payload);
        $responseData = $this->decodeResponse();
        $spotId = $responseData['id'];

        // Then, update the spot with given id
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'test update spot',
            'isFavorite' => true,
        ];

        $this->requestMethod('PUT', '/api/spots/'.$spotId, $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Check if update is OK
        $this->requestMethod('GET', '/api/spots/'.$spotId);
        $spotResponse = $this->decodeResponse();

        $this->assertEquals('test update spot', $spotResponse['description']);
        $this->assertEquals(true, $spotResponse['isFavorite']);
    }

    public function testUpdateSpotReturnsAccessDeniedException(): void
    {
        $this->authenticateUserWithJwt(self::OTHER_EXINSTING_USER_EMAIL);

        // Create Spot for the test for other user
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'spot to test GET',
        ];
        $this->requestMethod('POST', '/api/spots', $payload);
        $responseData = $this->decodeResponse();
        $spotId = $responseData['id'];

        // Now, authenticate principal user and call updateSpot with id of spot create juste before
        $this->authenticatePrincipalUserWithJwt();

        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'test update spot',
            'isFavorite' => true,
        ];

        $this->requestMethod('PUT', '/api/spots/'.$spotId, $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public static function updateSpotBadPayloadDataProvider(): array
    {
        return [
            'Description is number' => [[
                'latitude' => 45.421539,
                'longitude' => 5.685141,
                'description' => 123,
                'isFavorite' => true,
            ]],
            'Latitude as float string' => [[
                'latitude' => '45.421539',
                'longitude' => 5.685141,
                'description' => 'spot test',
                'isFavorite' => false,
            ]],
            // 'Missing isFavorite' => [[
            //     'latitude' => 45.421539,
            //     'longitude' => 5.685141,
            //     'description' => 'spot test',
            // ]]
        ];
    }

    #[Attributes\DataProvider('updateSpotBadPayloadDataProvider')]
    public function testUpdateSpotReturnsBadRequestException(array $payload): void
    {
        $this->authenticatePrincipalUserWithJwt();

        // Create Spot
        $payloadToCreate = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'test create spot',
        ];
        $this->requestMethod('POST', '/api/spots', $payloadToCreate);
        $responseData = $this->decodeResponse();
        $spotId = $responseData['id'];

        // Then, update the spot with given id
        $this->requestMethod('PUT', '/api/spots/'.$spotId, $payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    // ----------------------------------------------------------------------------
    // Delete Spot Tests
    // ----------------------------------------------------------------------------
    public function testDeleteSpotReturnsVoidSuccess(): void
    {
        $this->authenticatePrincipalUserWithJwt();

        $this->authenticatePrincipalUserWithJwt();

        // Create Spot for the test
        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'spot to test GET',
        ];
        $this->requestMethod('POST', '/api/spots', $payload);
        $responseData = $this->decodeResponse();
        $spotId = $responseData['id'];

        // Call deleteSpot method
        $this->requestMethod('DELETE', '/api/spots/'.$spotId);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    // ----------------------------------------------------------------------------
    // Get SpotList of friends Tests
    // ----------------------------------------------------------------------------
    // TODO

    // ----------------------------------------------------------------------------
    // Get Spot by Id of a friend Tests
    // ----------------------------------------------------------------------------
    // TODO

    // ----------------------------------------------------------------------------
    // Mutualized Exception Tests
    // ----------------------------------------------------------------------------
    public static function spotMethod_UnauthorizedException_DataProvider()
    {
        $baseuUri = '/api/spots';

        return [
            'Get Spots' => ['GET', $baseuUri],
            'Get Spot by Id' => ['GET', $baseuUri.'/1'],
            'Update Spot' => ['PUT', $baseuUri.'/1'],
            'Delete Spot' => ['DELETE', $baseuUri.'/1'],
            'Get Spots of friends' => ['GET', $baseuUri.'/friends'],
            'Get Spot of a friend by spotId' => ['GET', $baseuUri.'/1/friends'],
        ];
    }

    #[Attributes\DataProvider('spotMethod_UnauthorizedException_DataProvider')]
    public function testAllMethodsUnAuthorizedException(string $method, string $uri): void
    {
        // No login, no JWT in headers
        $this->requestMethod($method, $uri);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public static function spotMethod_NotFoundException_DataProvider()
    {
        $uri = '/api/spots/999999';

        $payload = [
            'latitude' => 45.421539,
            'longitude' => 5.685141,
            'description' => 'test update spot',
            'isFavorite' => true,
        ];

        return [
            'Get Spot by id' => ['GET', $uri],
            'Update Spot' => ['PUT', $uri, $payload],
            'Delete Spot' => ['DELETE', $uri],
        ];
    }

    #[Attributes\DataProvider('spotMethod_NotFoundException_DataProvider')]
    public function testAllMethodsNotFoundException(string $method, string $uri, ?array $payload = []): void
    {
        $this->authenticatePrincipalUserWithJwt();

        $this->requestMethod($method, $uri, $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
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

    private function authenticateUserWithJwt(string $user): void
    {
        $user = $this->userRepository->findByEmail($user);
        $token = $this->jwtManager->create($user);
        $this->client->setServerParameter('HTTP_Authorization', 'Bearer '.$token);
    }

    private function authenticatePrincipalUserWithJwt(): void
    {
        $this->authenticateUserWithJwt(self::EXINSTING_USER_EMAIL);
    }

    private function expectedSpotDTOKeys(): array
    {
        return [
            'latitude',
            'longitude',
            'description',
            'owner',
            'isFavorite',
            'id',
        ];
    }

    private function expectedSpotCollectionDTO(): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [],
                    ],
                    'properties' => [
                        'spotId' => null,
                        'ownerId' => null,
                    ],
                ],
            ],
        ];
    }
}

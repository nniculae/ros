<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @covers \App\Entity\User
 */
final class UserResourceTest extends CustomApiTestCase
{
    // GetCollection

    public function testAnAdminCanSeeAllUsers(): void
    {
        $userToken = $this->createUserToken();
        UserFactory::createMany(3);

        $response = self::createClient()->request('GET', '/api/users', [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();

        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4,
        ]);
    }

    public function testAUserCannotSeeOtherUsers(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        UserFactory::createOne(['roles' => [Role::ROLE_USER]]);

        $response = self::createClient()->request('GET', '/api/users', [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // Post

    public function testAnAdminCanCreateAUser(): void
    {
        $userToken = $this->createUserToken();

        $response = self::createClient()->request('POST', '/api/users', [
            'json' => [
                'email' => 'jan@example.nl',
                'roles' => [
                    Role::ROLE_USER,
                ],
                'password' => '123',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'email' => 'jan@example.nl',
            'roles' => [
                Role::ROLE_USER,
            ],
        ]);
        static::assertMatchesRegularExpression('~^/api/users/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testAUserCannotCreateAnotherUser(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);

        self::createClient()->request('POST', '/api/users', [
            'json' => [
                'email' => 'jan@example.nl',
                'password' => '123',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testItShouldNotCreateAUserWhileTheTokenIsInvalid(): void
    {
        $response = self::createClient()->request('POST', '/api/users', [
            'json' => [
                'email' => 'jan@example.nl',
                'password' => '123',
            ],
            'auth_bearer' => 'invalid_token',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    // Post register

    public function testItShouldRegisterAUser(): void
    {
        $response = self::createClient()->request('POST', '/api/users/register', [
            'json' => [
                'email' => 'jan@example.nl',
                'password' => '123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    // Get

    public function testAUserCanSeeHisDetails(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);

        $response = self::createClient()->request('GET', '/api/users/'.$userToken->userId, [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testAUserCannotSeeAnotherUserDetails(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);

        $user = UserFactory::createOne(['roles' => [Role::ROLE_USER]]);
        $userId = (string) $user->getId();

        $response = self::createClient()->request('GET', '/api/users/'.$userId, [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanSeeAnotherUser(): void
    {
        $userToken = $this->createUserToken();

        $user = UserFactory::createOne(['roles' => [Role::ROLE_USER]]);
        $userId = (string) $user->getId();

        $response = self::createClient()->request('GET', '/api/users/'.$userId, [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();
    }

    // Patch

    public function testAUserCanUpdateHisOwnPassword(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER], 'frank@zappa.nl');

        $response = self::createClient()->request('PATCH', '/api/users/update_password/'.$userToken->userId, [
            'json' => [
                'password' => '725',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testAnAdminCanUpdateEveryPassword(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_ADMIN], 'frank@zappa.nl');
        $user = UserFactory::createOne(['roles' => [Role::ROLE_USER]]);
        $userId = (string) $user->getId();

        $response = self::createClient()->request('PATCH', '/api/users/update_password/'.$userId, [
            'json' => [
                'password' => '725',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testAUserCannotUpdateThePasswordOfSomeoneElse(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER], 'frank@zappa.nl');
        $user = UserFactory::createOne(['roles' => [Role::ROLE_USER]]);
        $userId = (string) $user->getId();

        $response = self::createClient()->request('PATCH', '/api/users/update_password/'.$userId, [
            'json' => [
                'password' => '725',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // Put

    public function testAnAdminCanReplaceUserData(): void
    {
        $userToken = $this->createUserToken();
        $userId = (string) UserFactory::createOne()->getId();

        $response = self::createClient()->request('PUT', 'api/users/'.$userId, [
            'json' => [
                'email' => 'bobo@zappa.com',
                'password' => '3455678',
                'roles' => [Role::ROLE_ADMIN],
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'email' => 'bobo@zappa.com',
            'roles' => [Role::ROLE_ADMIN],
        ]);
    }

    public function testAUserCannnotReplaceUserData(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $userId = (string) UserFactory::createOne()->getId();

        $response = self::createClient()->request('PUT', 'api/users/'.$userId, [
            'json' => [
                'email' => 'bobo@zappa.com',
                'password' => '3455678',
                'roles' => [Role::ROLE_ADMIN],
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // Delete

    public function testAnAdminCanDeleteAUser(): void
    {
        $userToken = $this->createUserToken();
        $userId = (string) UserFactory::createOne()->getId();

        $response = self::createClient()->request('DELETE', 'api/users/'.$userId, [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testAUserCannotDeleteAUser(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $userId = (string) UserFactory::createOne()->getId();

        $response = self::createClient()->request('DELETE', 'api/users/'.$userId, [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    // Post login_check

    public function testItShouldReturnAToken(): void
    {
        UserFactory::createOne(['email' => 'bob@example.nl', 'password' => '123']);

        $response = self::createClient()->request('POST', '/api/login_check', [
            'json' => [
                'email' => 'bob@example.nl',
                'password' => '123',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $token = $response->toArray();
        static::assertArrayHasKey('token', $token);
        static::assertNotEmpty($token['token']);
    }
}

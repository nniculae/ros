<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Factory\AddressFactory;
use App\Factory\UserFactory;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @covers \App\Entity\Address
 */
final class AddressResourceTest extends CustomApiTestCase
{
    // GetCollection

    public function testAUserCannotViewAdresses(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        AddressFactory::createOne(['user' => $userToken->user]);

        self::createClient()->request('GET', '/api/addresses', [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanViewAdresses(): void
    {
        $userToken = $this->createUserToken();
        AddressFactory::createOne(['user' => $userToken->user]);

        self::createClient()->request('GET', '/api/addresses', [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();
    }

    // POST

    public function testAUserCanCreateAnAddress(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);

        self::createClient()->request('POST', '/api/addresses', [
            'json' => [
                'firstName' => 'Bob',
                'lastName' => 'Marley',
                'phoneNumber' => '06123456',
                'street' => 'Binnenhof 124',
                'city' => 'Goes',
                'postcode' => '8723AB',
                'user' => 'api/users/'.$userToken->userId,
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testAUserCannotCreateAnAddressForAnotherUser(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $user = UserFactory::createOne(['email' => 'user@zappa.nl', 'password' => '123']);

        self::createClient()->request('POST', '/api/addresses', [
            'json' => [
                'firstName' => 'Bob',
                'lastName' => 'Marley',
                'phoneNumber' => '06123456',
                'street' => 'Binnenhof 124',
                'city' => 'Goes',
                'postcode' => '8723AB',
                'user' => 'api/users/'.$user->getId(),
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanCreateAnAddressForAnotherUser(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_ADMIN]);
        $user = UserFactory::createOne(['email' => 'user@zappa.nl', 'password' => '123']);

        self::createClient()->request('POST', '/api/addresses', [
            'json' => [
                'firstName' => 'Bob',
                'lastName' => 'Marley',
                'phoneNumber' => '06123456',
                'street' => 'Binnenhof 124',
                'city' => 'Goes',
                'postcode' => '8723AB',
                'user' => 'api/users/'.$user->getId(),
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    // PUT, PATCH

    public function testAUserCanEditHisAddresses(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $address = AddressFactory::createOne(['user' => $userToken->user]);

        self::createClient()->request('PUT', '/api/addresses/'.$address->getId(), [
            'json' => [
                'firstName' => 'Jimi',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        $this->assertResponseIsSuccessful();

        self::createClient()->request('PATCH', '/api/addresses/'.$address->getId(), [
            'json' => [
                'firstName' => 'Jimi',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testAUserCannotEditAddressesOfOtherUsers(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $user = UserFactory::createOne();

        $address = AddressFactory::createOne(['user' => $user]);

        self::createClient()->request('PUT', '/api/addresses/'.$address->getId(), [
            'json' => [
                'firstName' => 'Jimi',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        self::createClient()->request('PATCH', '/api/addresses/'.$address->getId(), [
            'json' => [
                'firstName' => 'Bob',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanEditAllAddresses(): void
    {
        $userToken = $this->createUserToken();
        $user = UserFactory::createOne();
        $address = AddressFactory::createOne(['user' => $user]);

        self::createClient()->request('PUT', '/api/addresses/'.$address->getId(), [
            'json' => [
                'firstName' => 'Jimi',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        $this->assertResponseIsSuccessful();

        self::createClient()->request('PATCH', '/api/addresses/'.$address->getId(), [
            'json' => [
                'firstName' => 'Bob',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseIsSuccessful();
    }

    // DELETE

    public function testAUserCannotDeleteHisAddresses(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $address = AddressFactory::createOne(['user' => $userToken->user]);

        self::createClient()->request('DELETE', 'api/addresses/'.$address->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanDeleteAllAddresses(): void
    {
        $userToken = $this->createUserToken();
        $address = AddressFactory::createOne(['user' => $userToken->user]);
        $secondAddress = AddressFactory::createOne(['user' => UserFactory::createOne()]);

        self::createClient()->request('DELETE', 'api/addresses/'.$address->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        self::createClient()->request('DELETE', 'api/addresses/'.$secondAddress->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}

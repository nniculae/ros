<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Factory\CategoryFactory;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @covers \App\Entity\Category
 */
final class CategoryResourceTest extends CustomApiTestCase
{
    // GetCollection, GET

    public function testEveryoneCanViewCategories(): void
    {
        $category = CategoryFactory::createOne();

        self::createClient()->request('GET', '/api/categories');

        self::assertResponseIsSuccessful();

        self::createClient()->request('GET', '/api/categories/'.$category->getId());

        self::assertResponseIsSuccessful();
    }

    // POST, PUT, PATCH, DELETE

    public function testAUserCannotProcessCategories(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);

        self::createClient()->request('POST', '/api/categories', [
            'json' => [
                'name' => 'Fish',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $category = CategoryFactory::createOne();

        self::createClient()->request('PUT', '/api/categories/'.$category->getId(), [
            'json' => [
                'name' => 'Fish',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        self::createClient()->request('PATCH', '/api/categories/'.$category->getId(), [
            'json' => [
                'name' => 'Fish',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        self::createClient()->request('DELETE', '/api/categories/'.$category->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanProcessCategories(): void
    {
        $userToken = $this->createUserToken();

        self::createClient()->request('POST', '/api/categories', [
            'json' => [
                'name' => 'Fish',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $category = CategoryFactory::createOne();

        self::createClient()->request('PUT', '/api/categories/'.$category->getId(), [
            'json' => [
                'name' => 'Fish',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();

        self::createClient()->request('PATCH', '/api/categories/'.$category->getId(), [
            'json' => [
                'name' => 'Fish',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        self::assertResponseIsSuccessful();

        self::createClient()->request('DELETE', '/api/categories/'.$category->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}

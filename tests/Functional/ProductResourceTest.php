<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @covers \App\Entity\Product
 */
final class ProductResourceTest extends CustomApiTestCase
{
    // GetCollection, GET

    public function testEveryoneCanViewProducts(): void
    {
        $product = ProductFactory::createOne(['category' => CategoryFactory::createOne()]);

        self::createClient()->request('GET', '/api/products');

        self::assertResponseIsSuccessful();

        self::createClient()->request('GET', '/api/products/'.$product->getId());

        self::assertResponseIsSuccessful();
    }

    // POST, PUT, PATCH, DELETE

    public function testAUserCannotProcessProducts(): void
    {
        $userToken = $this->createUserToken([Role::ROLE_USER]);
        $category = CategoryFactory::createOne();

        self::createClient()->request('POST', '/api/products', [
            'json' => [
                'name' => 'Zalm met frietjes',
                'price' => '12.56',
                'category' => '/api/categories/'.$category->getId(),
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $product = ProductFactory::createOne(['category' => CategoryFactory::createOne()]);

        self::createClient()->request('PUT', '/api/products/'.$product->getId(), [
            'json' => [
                'name' => 'Kip Tandori',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        self::createClient()->request('PATCH', '/api/products/'.$product->getId(), [
            'json' => [
                'name' => 'Kip Tandori',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        self::createClient()->request('DELETE', '/api/products/'.$product->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAnAdminCanProcessProducts(): void
    {
        $userToken = $this->createUserToken();
        $category = CategoryFactory::createOne();

        self::createClient()->request('POST', '/api/products', [
            'json' => [
                'name' => 'Zalm met frietjes',
                'price' => '12.56',
                'category' => '/api/categories/'.$category->getId(),
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $product = ProductFactory::createOne(['category' => CategoryFactory::createOne()]);

        self::createClient()->request('PUT', '/api/products/'.$product->getId(), [
            'json' => [
                'name' => 'Kip Tandori',
            ],
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseIsSuccessful();

        self::createClient()->request('PATCH', '/api/products/'.$product->getId(), [
            'json' => [
                'name' => 'Kip Tandori',
            ],
            'auth_bearer' => $userToken->token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        self::assertResponseIsSuccessful();

        self::createClient()->request('DELETE', '/api/products/'.$product->getId(), [
            'auth_bearer' => $userToken->token,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use App\Security\Role;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class CustomApiTestCase extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    protected function createUserToken(array $roles = [Role::ROLE_ADMIN], string $email = 'user@example.nl'): UserToken
    {
        $password = '123_secret';

        $user = UserFactory::createOne(['email' => $email, 'password' => '123_secret', 'roles' => $roles]);

        $response = self::createClient()->request(
            'POST',
            '/api/login_check',
            [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]
        );

        $data = $response->toArray();

        return new UserToken($data['token'], $user->object());
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;

final class UserToken
{
    public string $userId;

    public function __construct(public string $token, public User $user)
    {
        $this->userId = (string) $user->getId();
    }
}

<?php

declare(strict_types=1);

namespace App\Dto\Request\User;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserRegisterDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 254)]
    #[Groups(['user:read', 'user:write'])]
    public string $email;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3)] // TODO: Increase the minimum length
    #[Groups(['user:read', 'user:write'])]
    public string $password;
}

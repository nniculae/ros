<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $doctrinePersistProcessor,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @param User $user
     */
    public function process(mixed $user, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        return $this->doctrinePersistProcessor->process($user, $operation, $uriVariables, $context);
    }
}

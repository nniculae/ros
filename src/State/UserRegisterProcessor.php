<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Request\User\UserRegisterDto;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegisterProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $doctrinePersistProcessor,
        private UserPasswordHasherInterface $userPasswordHasher,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @param UserRegisterDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $user = new User();
        $user->setEmail($data->email);
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $data->password);
        $user->setPassword($hashedPassword);

        // In order to trigger UniqueEntity validator
        $this->validator->validate($user, $context);

        return $this->doctrinePersistProcessor->process($user, $operation, $uriVariables, $context);
    }
}

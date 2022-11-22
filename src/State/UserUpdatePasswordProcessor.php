<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Request\User\UserUpdatePasswordDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserUpdatePasswordProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $doctrinePersistProcessor,
        private UserPasswordHasherInterface $userPasswordHasher,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param UserUpdatePasswordDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var User */
        $userToBeUpdated = $this->entityManager->find(User::class, $uriVariables['id']);

        $hashedPassword = $this->userPasswordHasher->hashPassword($userToBeUpdated, $data->password);
        $userToBeUpdated->setPassword($hashedPassword);

        // In order to trigger UniqueEntity validator
        $this->validator->validate($userToBeUpdated, $context);

        return $this->doctrinePersistProcessor->process($userToBeUpdated, $operation, $uriVariables, $context);
    }
}

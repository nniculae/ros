<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class RolesValidator extends ConstraintValidator
{
    public function validate($roles, Constraint $constraint): void
    {
        // @var App\Validator\Roles $constraint

        if (null === $roles || '' === $roles) {
            return;
        }

        if (!\is_array($roles)) {
            throw new UnexpectedValueException($roles, 'array');
        }

        $diff = array_diff($roles, $constraint->roles);

        if (\count($diff) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ roles }}', json_encode($roles))
                ->addViolation()
            ;
        }
    }
}

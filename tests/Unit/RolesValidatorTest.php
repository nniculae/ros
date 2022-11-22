<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Validator\Roles;
use App\Validator\RolesValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException as ExceptionUnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 *
 * @covers \App\Validator\RolesValidator
 *
 * @uses \App\Validator\Roles
 */
final class RolesValidatorTest extends ConstraintValidatorTestCase
{
    private array $validRoles = ['ROLE_USER', 'ROLE_ADMIN'];

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Roles($this->validRoles));
        self::assertNoViolation();
    }

    public function testArgumentIsNotAnArray(): void
    {
        self::expectException(ExceptionUnexpectedValueException::class);
        $this->validator->validate('mama', new Roles($this->validRoles));
    }

    /**
     * @dataProvider provideValidRoles
     */
    public function testValidRoles(array $validRoles): void
    {
        $this->validator->validate($validRoles, new Roles($this->validRoles));
        self::assertNoViolation();
    }

    public function provideValidRoles(): iterable
    {
        yield [['ROLE_ADMIN']];

        yield [['ROLE_USER']];

        yield [['ROLE_ADMIN', 'ROLE_USER']];

        yield [['ROLE_USER', 'ROLE_ADMIN']];

        yield [[]];
    }

    /**
     * @dataProvider provideInvalidRoles
     */
    public function testInvalidRoles(array $invalidRoles): void
    {
        $this->validator->validate($invalidRoles, new Roles($this->validRoles));

        self::buildViolation('One or more roles are not valid: "{{ roles }}".')
            ->setParameter('{{ roles }}', json_encode($invalidRoles))
            ->assertRaised()
        ;
    }

    public function provideInvalidRoles(): iterable
    {
        yield [['ROLE_ADMIN', 'ROLE_HACKER']];

        yield [['ROLE_ADMIN', 'ROLE_USER', 'ROLE_BOSS']];
    }

    protected function createValidator()
    {
        return new RolesValidator();
    }
}

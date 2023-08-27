<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Validator\MoneyCurrency;
use App\Validator\MoneyCurrencyValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class MoneyCurrencyValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new MoneyCurrencyValidator();
    }

    public function testNullIsInvalid(): void
    {
        $this->validator->validate(null, new MoneyCurrency());

        $this->buildViolation('Provided value for currency is invalid')
            ->assertRaised();
    }

    /**
     * @dataProvider provideInvalidConstraints
     */
    public function testTrueIsInvalid(MoneyCurrency $constraint): void
    {
        $this->validator->validate('...', $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ string }}', '...')
            ->assertRaised();
    }

    public function provideInvalidConstraints(): iterable
    {
        yield [new MoneyCurrency(message: 'myMessage')];
    }
}

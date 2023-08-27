<?php

declare(strict_types=1);

namespace App\Validator;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MoneyCurrencyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!is_string($value) || strlen($value) !== 3) {
            $this->context->buildViolation('Provided value for currency is invalid')
                ->addViolation();

            return;
        }

        $currencies = new ISOCurrencies();

        if (!$currencies->contains(new Currency($value))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}

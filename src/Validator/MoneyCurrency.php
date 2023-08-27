<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Money\Currencies\ISOCurrencies;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\LogicException;

#[Attribute]
final class MoneyCurrency extends Constraint
{
    public string $message = 'The currency "{{ string }}" is not valid';

    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        if (!class_exists(ISOCurrencies::class)) {
            throw new LogicException(
                'The moneyPHP component is required to use the MoneyCurrency constraint. 
                Try running "composer require moneyphp/money".'
            );
        }

        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}

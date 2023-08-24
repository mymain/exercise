<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeMoneyDto
{
    public function __construct(
        #[Assert\Currency]
        public readonly string $baseCurrency,
        #[Assert\GreaterThan(1)]
        public readonly float $baseAmount,
        #[Assert\Currency]
        public readonly string $targetCurrency,
    ) {
    }
}

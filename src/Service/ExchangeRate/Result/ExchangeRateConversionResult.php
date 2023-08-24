<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Result;

use Money\Currency;
use Money\Money;

final class ExchangeRateConversionResult
{
    public function __construct(
        public readonly Currency $baseCurrency,
        public readonly Currency $targetCurrency,
        public readonly Money $baseAmount,
        public readonly Money $targetAmount,
        public readonly float $exchangeRate,
    ) {
    }
}

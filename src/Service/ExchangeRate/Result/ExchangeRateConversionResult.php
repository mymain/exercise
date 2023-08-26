<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Result;

use Money\Currency;
use Money\Money;

final class ExchangeRateConversionResult implements ExchangeRateConversionResultInterface
{
    public function __construct(
        private readonly Currency $baseCurrency,
        private readonly Currency $targetCurrency,
        private readonly Money $baseAmount,
        private readonly Money $targetAmount,
        private readonly float $exchangeRate,
    ) {
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }

    public function getBaseAmount(): Money
    {
        return $this->baseAmount;
    }

    public function getTargetAmount(): Money
    {
        return $this->targetAmount;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }
}

<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate;

use App\Service\ExchangeRate\Exception\RateNotFoundException;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResult;
use Money\Currency;
use Money\Money;

final class ExchangeRateConverter
{
    private const MULTIPLIER = 100;

    public function __construct(
        Currency $baseCurrency,
        private array $rates,
    ) {
        $this->rates[$baseCurrency->getCode()] = 1.0;
    }

    public function convert(
        Money $baseAmount,
        Currency $baseCurrency,
        Currency $targetCurrency
    ): ExchangeRateConversionResult {
        $toCode = $targetCurrency->getCode();
        $fromCode = $baseCurrency->getCode();
        $targetAmount = clone $baseAmount;

        if (!isset($this->rates[$toCode])) {
            throw new RateNotFoundException($toCode);
        }

        if (!isset($this->rates[$fromCode])) {
            throw new RateNotFoundException($fromCode);
        }

        $targetAmount = $targetAmount->multiply($this->rates[$toCode] * self::MULTIPLIER)
            ->divide($this->rates[$fromCode] * self::MULTIPLIER);

        return new ExchangeRateConversionResult(
            baseCurrency: $baseCurrency,
            targetCurrency: $targetCurrency,
            baseAmount: $baseAmount,
            targetAmount: $targetAmount,
            exchangeRate: $this->rates[$toCode] / $this->rates[$fromCode],
        );
    }
}

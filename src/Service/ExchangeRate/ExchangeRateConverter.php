<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate;

use App\Service\ExchangeRate\Exception\RateNotFoundException;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResult;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use Money\Currency;
use Money\Money;

final class ExchangeRateConverter implements ExchangeRateConverterInterface
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
        Currency $targetCurrency,
    ): ExchangeRateConversionResultInterface {
        $toCode = $targetCurrency->getCode();
        $fromCode = $baseCurrency->getCode();
        $targetAmount = clone $baseAmount;

        $toRate = $this->rates[$toCode] ?? throw new RateNotFoundException($toCode);
        $fromRate = $this->rates[$fromCode] ?? throw new RateNotFoundException($fromCode);

        $targetAmount = $targetAmount->multiply($toRate * self::MULTIPLIER)
            ->divide($fromRate * self::MULTIPLIER);

        return new ExchangeRateConversionResult(
            baseCurrency: $baseCurrency,
            targetCurrency: $targetCurrency,
            baseAmount: $baseAmount,
            targetAmount: $targetAmount,
            exchangeRate: $toRate / $fromRate,
        );
    }
}

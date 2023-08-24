<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate;

use App\Service\ExchangeRate\Exception\RateNotFoundException;
use Money\Currency;
use Money\Money;

final class ExchangeRateCalculator
{
    private const MULTIPLIER = 100;

    public function __construct(
        string $baseCurrency,
        private int $timestamp,
        private array $rates,
    ) {
        $this->rates[$baseCurrency] = 1.0;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function convert(Money $amount, Currency $baseCurrency, Currency $targetCurrency): Money
    {
        $toCode = $targetCurrency->getCode();
        $fromCode = $baseCurrency->getCode();

        if (!isset($this->rates[$toCode])) {
            throw new RateNotFoundException($toCode);
        }

        if (!isset($this->rates[$fromCode])) {
            throw new RateNotFoundException($fromCode);
        }

        return $amount->multiply($this->rates[$toCode] * self::MULTIPLIER)
            ->divide($this->rates[$fromCode] * self::MULTIPLIER);
    }
}

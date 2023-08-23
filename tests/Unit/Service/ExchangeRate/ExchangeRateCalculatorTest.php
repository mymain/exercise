<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Service\ExchangeRate\ExchangeRateCalculator;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class ExchangeRateCalculatorTest extends TestCase
{
    private ExchangeRateCalculator $object;

    public function setUp(): void
    {
        $this->object = new ExchangeRateCalculator(
            baseCurrency: 'EUR',
            timestamp: 123,
            rates: [
                'PLN' => 4.46698,
            ]
        );
    }

    public function testConvert(): void
    {
        $conversionResult = $this->object->convert(
            Money::EUR(100),
            new Currency('EUR'),
            new Currency('PLN')
        );

        $this->assertInstanceOf(
            Money::class,
            $conversionResult,
        );

        $this->assertEquals(
            447,
            $conversionResult->getAmount()
        );
    }
}

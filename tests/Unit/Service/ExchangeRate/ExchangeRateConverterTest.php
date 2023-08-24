<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Service\ExchangeRate\Exception\RateNotFoundException;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResult;
use App\Service\ExchangeRate\ExchangeRateConverter;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class ExchangeRateConverterTest extends TestCase
{
    private ExchangeRateConverter $object;

    public function setUp(): void
    {
        $this->object = new ExchangeRateConverter(
            baseCurrency: new Currency('EUR'),
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
            new Currency('PLN'),
        );

        $this->assertInstanceOf(
            ExchangeRateConversionResult::class,
            $conversionResult,
        );

        $this->assertEquals(
            447,
            $conversionResult->targetAmount->getAmount(),
        );
    }

    public function testException(): void
    {
        $this->expectException(RateNotFoundException::class);
        $this->expectExceptionMessage('Conversion rate for USD not found');

        $this->object->convert(
            Money::EUR(100),
            new Currency('EUR'),
            new Currency('USD'),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Entity\Transaction;
use App\Factory\TransactionFactory;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;

final class TransactionFactoryTest extends TestCase
{
    private const TIMESTAMP = 1692886830;

    private TransactionFactory $object;

    private ClockInterface $clockMock;
    private ExchangeRateConversionResultInterface $exchangeRateConversionResultMock;

    public function setUp(): void
    {
        $this->clockMock = $this->createMock(ClockInterface::class);
        $this->exchangeRateConversionResultMock = $this->createMock(
            ExchangeRateConversionResultInterface::class
        );

        $this->object = new TransactionFactory($this->clockMock);
    }

    public function testFromExchangeConversionResult(): void
    {
        $this->exchangeRateConversionResultMock->expects($this->once())
            ->method('getBaseCurrency')
            ->willReturn(new Currency('PLN'));

        $this->exchangeRateConversionResultMock->expects($this->once())
            ->method('getTargetCurrency')
            ->willReturn(new Currency('USD'));

        $this->exchangeRateConversionResultMock->expects($this->once())
            ->method('getBaseAmount')
            ->willReturn(Money::PLN(123));

        $this->exchangeRateConversionResultMock->expects($this->once())
            ->method('getTargetAmount')
            ->willReturn(Money::USD(321));

        $this->exchangeRateConversionResultMock->expects($this->once())
            ->method('getExchangeRate')
            ->willReturn(1.23);

        $this->clockMock->expects($this->once())
            ->method('now')
            ->willReturn(
                (new DateTimeImmutable())
                ->setTimestamp(self::TIMESTAMP)
            );

        $transaction = $this->object->fromExchangeConversionResult(
            $this->exchangeRateConversionResultMock,
            'unit-test'
        );

        $this->assertInstanceOf(Transaction::class, $transaction);

        $this->assertSame(
            'card',
            $transaction->getPaymentMethod()
        );

        $this->assertSame(
            'deposit',
            $transaction->getTransactionType()
        );

        $this->assertSame(
            self::TIMESTAMP,
            $transaction->getTransactionTimestamp()
        );

        $this->assertSame(
            123,
            $transaction->getBaseAmount()
        );

        $this->assertSame(
            'PLN',
            $transaction->getBaseCurrency()
        );

        $this->assertSame(
            321,
            $transaction->getTargetAmount()
        );

        $this->assertSame(
            'USD',
            $transaction->getTargetCurrency()
        );

        $this->assertSame(
            1.23,
            $transaction->getExchangeRate()
        );

        $this->assertSame(
            'unit-test',
            $transaction->getIp()
        );
    }
}

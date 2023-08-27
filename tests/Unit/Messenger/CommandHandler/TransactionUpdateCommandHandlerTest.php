<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\CommandHandler;

use App\Dto\TransactionUpdateDto;
use App\Entity\Transaction;
use App\Factory\TransactionFactoryInterface;
use App\Messenger\Command\TransactionUpdateCommand;
use App\Messenger\CommandHandler\TransactionUpdateCommandHandler;
use App\Repository\TransactionRepositoryInterface;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactoryInterface;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class TransactionUpdateCommandHandlerTest extends TestCase
{
    private TransactionUpdateCommandHandler $object;

    private TransactionFactoryInterface $transactionFactoryMock;
    private TransactionRepositoryInterface $transactionRepositoryMock;
    private ExchangeRateConverterInterface $exchangeRateConverterMock;
    private ExchangeRateConversionResultInterface $exchangeConversionResultMock;
    private ExchangeRateDataProviderFactoryInterface $exchangeRateDataProviderFactoryMock;

    public function setUp(): void
    {
        $this->transactionFactoryMock = $this->createMock(TransactionFactoryInterface::class);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->exchangeRateConverterMock = $this->createMock(ExchangeRateConverterInterface::class);
        $this->exchangeConversionResultMock = $this->createMock(ExchangeRateConversionResultInterface::class);
        $this->exchangeRateDataProviderFactoryMock = $this->createMock(ExchangeRateDataProviderFactoryInterface::class);

        $this->object = new TransactionUpdateCommandHandler(
            exchangeRateDataProviderFactory: $this->exchangeRateDataProviderFactoryMock,
            transactionRepository: $this->transactionRepositoryMock,
            transactionFactory: $this->transactionFactoryMock,
        );
    }

    public function testInvoke(): void
    {
        $transactionUpdateDto = new TransactionUpdateDto(
            transactionId: 1,
            targetCurrency: 'PLN',
        );

        $transaction = (new Transaction())
            ->setBaseCurrency('PLN')
            ->setBaseAmount(100)
            ->setTargetCurrency($transactionUpdateDto->targetCurrency);

        $this->transactionRepositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn($transaction);

        $this->exchangeRateDataProviderFactoryMock->expects($this->once())
            ->method('getDataProvider')
            ->willReturn($this->exchangeRateConverterMock);

        $this->exchangeRateConverterMock->expects($this->once())
            ->method('convert')
            ->with(
                new Money($transaction->getBaseAmount(), new Currency($transaction->getBaseCurrency())),
                new Currency($transaction->getBaseCurrency()),
                new Currency($transactionUpdateDto->targetCurrency),
            )
            ->willReturn($this->exchangeConversionResultMock);


        $this->exchangeConversionResultMock->expects($this->once())
            ->method('getTargetCurrency')
            ->willReturn(new Currency('PLN'));

        $this->exchangeConversionResultMock->expects($this->once())
            ->method('getTargetAmount')
            ->willReturn(new Money(200, new Currency('PLN')));

        $this->exchangeConversionResultMock->expects($this->once())
            ->method('getExchangeRate')
            ->willReturn(1.234);

        $this->transactionRepositoryMock->expects($this->once())
            ->method('flush');

        $transaction = $this->object->__invoke(new TransactionUpdateCommand(
            transactionUpdateDto: $transactionUpdateDto,
        ));

        $this->assertInstanceOf(
            Transaction::class,
            $transaction
        );

        $this->assertEquals(
            100,
            $transaction->getBaseAmount()
        );

        $this->assertEquals(
            'PLN',
            $transaction->getBaseCurrency()
        );

        $this->assertEquals(
            200,
            $transaction->getTargetAmount()
        );

        $this->assertEquals(
            'PLN',
            $transaction->getTargetCurrency()
        );

        $this->assertEquals(
            1.234,
            $transaction->getExchangeRate()
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\CommandHandler;

use App\Dto\TransactionExchangeDto;
use App\Entity\Transaction;
use App\Factory\TransactionFactory;
use App\Messenger\Command\TransactionExchangeCommand;
use App\Messenger\CommandHandler\TransactionExchangeCommandHandler;
use App\Repository\TransactionRepository;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use DG\BypassFinals;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class TransactionExchangeCommandHandlerTest extends TestCase
{
    private TransactionExchangeCommandHandler $object;

    private TransactionFactory $transactionFactoryMock;
    private TransactionRepository $transactionRepositoryMock;
    private ExchangeRateConverterInterface $exchangeRateConverterMock;
    private ExchangeRateConversionResultInterface $exchangeConversionResultMock;
    private ExchangeRateDataProviderFactory $exchangeRateDataProviderFactoryMock;

    public function setUp(): void
    {
        BypassFinals::enable();

        $this->transactionFactoryMock = $this->createMock(TransactionFactory::class);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);
        $this->exchangeRateConverterMock = $this->createMock(ExchangeRateConverterInterface::class);
        $this->exchangeConversionResultMock = $this->createMock(ExchangeRateConversionResultInterface::class);
        $this->exchangeRateDataProviderFactoryMock = $this->createMock(ExchangeRateDataProviderFactory::class);

        $this->object = new TransactionExchangeCommandHandler(
            exchangeRateDataProviderFactory: $this->exchangeRateDataProviderFactoryMock,
            transactionRepository: $this->transactionRepositoryMock,
            transactionFactory: $this->transactionFactoryMock,
        );
    }

    public function testInvoke(): void
    {
        $transactionExchangeDto = new TransactionExchangeDto(
            baseCurrency: 'EUR',
            baseAmount: 100,
            targetCurrency: 'PLN',
        );

        $this->exchangeRateDataProviderFactoryMock->expects($this->once())
            ->method('getDataProvider')
            ->willReturn($this->exchangeRateConverterMock);

        $this->exchangeRateConverterMock->expects($this->once())
            ->method('convert')
            ->with(
                new Money(
                    $transactionExchangeDto->baseAmount,
                    new Currency($transactionExchangeDto->baseCurrency)
                ),
                new Currency($transactionExchangeDto->baseCurrency),
                new Currency($transactionExchangeDto->targetCurrency),
            )
            ->willReturn($this->exchangeConversionResultMock);

        $transaction = new Transaction();

        $this->transactionFactoryMock->expects($this->once())
            ->method('fromExchangeConversionResult')
            ->with(
                $this->exchangeConversionResultMock,
                'unit-test'
            )
            ->willReturn($transaction);

        $this->transactionRepositoryMock->expects($this->once())
            ->method('persist')
            ->with($transaction);

        $result = $this->object->__invoke(new TransactionExchangeCommand(
            transactionExchangeDto: $transactionExchangeDto,
            ip: 'unit-test',
        ));

        $this->assertInstanceOf(
            Transaction::class,
            $result
        );
    }
}

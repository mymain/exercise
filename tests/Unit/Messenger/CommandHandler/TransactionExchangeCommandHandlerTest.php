<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\CommandHandler;

use App\Dto\TransactionExchangeDto;
use App\Entity\Transaction;
use App\Factory\TransactionFactoryInterface;
use App\Messenger\Command\TransactionExchangeCommand;
use App\Messenger\CommandHandler\TransactionExchangeCommandHandler;
use App\Repository\TransactionRepositoryInterface;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactoryInterface;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class TransactionExchangeCommandHandlerTest extends TestCase
{
    private TransactionExchangeCommandHandler $object;

    private Transaction $transactionMock;
    private MessageBusInterface $eventBusMock;
    private TransactionFactoryInterface $transactionFactoryMock;
    private TransactionRepositoryInterface $transactionRepositoryMock;
    private ExchangeRateConverterInterface $exchangeRateConverterMock;
    private ExchangeRateConversionResultInterface $exchangeConversionResultMock;
    private ExchangeRateDataProviderFactoryInterface $exchangeRateDataProviderFactoryMock;

    public function setUp(): void
    {
        $this->transactionMock = $this->createMock(Transaction::class);
        $this->eventBusMock = $this->createMock(MessageBusInterface::class);
        $this->transactionFactoryMock = $this->createMock(TransactionFactoryInterface::class);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->exchangeRateConverterMock = $this->createMock(ExchangeRateConverterInterface::class);
        $this->exchangeConversionResultMock = $this->createMock(ExchangeRateConversionResultInterface::class);
        $this->exchangeRateDataProviderFactoryMock = $this->createMock(ExchangeRateDataProviderFactoryInterface::class);

        $this->object = new TransactionExchangeCommandHandler(
            exchangeRateDataProviderFactory: $this->exchangeRateDataProviderFactoryMock,
            transactionRepository: $this->transactionRepositoryMock,
            transactionFactory: $this->transactionFactoryMock,
            eventBus: $this->eventBusMock,
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

        $this->transactionFactoryMock->expects($this->once())
            ->method('fromExchangeConversionResult')
            ->with(
                $this->exchangeConversionResultMock,
                'unit-test'
            )
            ->willReturn($this->transactionMock);

        $this->transactionRepositoryMock->expects($this->once())
            ->method('persist')
            ->with($this->transactionMock);

        $this->eventBusMock->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(
                $this->object,
                [new HandledStamp($this->object, 'handler-name')]
            ));

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

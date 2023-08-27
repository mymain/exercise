<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\CommandHandler;

use App\Dto\TransactionExchangeDto;
use App\Entity\Transaction;
use App\Messenger\Command\TransactionExchangeCommand;
use App\Messenger\CommandHandler\TransactionExchangeCommandHandler;
use Money\Currency;
use Money\Money;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class TransactionExchangeCommandHandlerTest extends AbstractCommandHandlerTest
{
    private TransactionExchangeCommandHandler $object;

    public function setUp(): void
    {
        parent::setUp();

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

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\CommandHandler;

use App\Dto\TransactionUpdateDto;
use App\Entity\Transaction;
use App\Messenger\Command\TransactionUpdateCommand;
use App\Messenger\CommandHandler\TransactionUpdateCommandHandler;
use Money\Currency;
use Money\Money;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class TransactionUpdateCommandHandlerTest extends AbstractCommandHandlerTest
{
    private TransactionUpdateCommandHandler $object;

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new TransactionUpdateCommandHandler(
            exchangeRateDataProviderFactory: $this->exchangeRateDataProviderFactoryMock,
            transactionRepository: $this->transactionRepositoryMock,
            transactionFactory: $this->transactionFactoryMock,
            eventBus: $this->eventBusMock,
        );
    }

    public function testInvoke(): void
    {
        $transactionUpdateDto = new TransactionUpdateDto(
            transactionId: 1,
            targetCurrency: 'PLN',
        );

        $this->transactionMock->expects($this->once())
            ->method('getId')
            ->willReturn(324);

        $this->transactionMock->expects($this->exactly(2))
            ->method('getBaseCurrency')
            ->willReturn('PLN');

        $this->transactionMock->expects($this->once())
            ->method('getBaseAmount')
            ->willReturn(100);

        $this->transactionMock->expects($this->once())
            ->method('setTargetCurrency')
            ->with('PLN')
            ->willReturnSelf();

        $this->transactionMock->expects($this->once())
            ->method('setTargetAmount')
            ->with(200)
            ->willReturnSelf();

        $this->transactionMock->expects($this->once())
            ->method('setExchangeRate')
            ->with(1.234)
            ->willReturnSelf();

        $this->transactionRepositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn($this->transactionMock);

        $this->exchangeRateDataProviderFactoryMock->expects($this->once())
            ->method('getDataProvider')
            ->willReturn($this->exchangeRateConverterMock);

        $this->exchangeRateConverterMock->expects($this->once())
            ->method('convert')
            ->with(
                new Money(100, new Currency('PLN')),
                new Currency('PLN'),
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

        $this->eventBusMock->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(
                $this->object,
                [new HandledStamp($this->object, 'handler-name')]
            ));

        $transaction = $this->object->__invoke(new TransactionUpdateCommand(
            transactionUpdateDto: $transactionUpdateDto,
        ));

        $this->assertInstanceOf(
            Transaction::class,
            $transaction
        );
    }
}

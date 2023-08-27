<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messenger\CommandHandler;

use App\Entity\Transaction;
use App\Factory\TransactionFactoryInterface;
use App\Repository\TransactionRepositoryInterface;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactoryInterface;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractCommandHandlerTest extends TestCase
{
    protected Transaction $transactionMock;
    protected MessageBusInterface $eventBusMock;
    protected TransactionFactoryInterface $transactionFactoryMock;
    protected TransactionRepositoryInterface $transactionRepositoryMock;
    protected ExchangeRateConverterInterface $exchangeRateConverterMock;
    protected ExchangeRateConversionResultInterface $exchangeConversionResultMock;
    protected ExchangeRateDataProviderFactoryInterface $exchangeRateDataProviderFactoryMock;

    protected function setUp(): void
    {
        $this->transactionMock = $this->createMock(Transaction::class);
        $this->eventBusMock = $this->createMock(MessageBusInterface::class);
        $this->transactionFactoryMock = $this->createMock(TransactionFactoryInterface::class);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->exchangeRateConverterMock = $this->createMock(ExchangeRateConverterInterface::class);
        $this->exchangeConversionResultMock = $this->createMock(ExchangeRateConversionResultInterface::class);
        $this->exchangeRateDataProviderFactoryMock = $this->createMock(ExchangeRateDataProviderFactoryInterface::class);
    }
}

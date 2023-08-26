<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\ExchangeRate;

use App\Service\ExchangeRate\DataProvider\DataProviderInterface;
use App\Service\ExchangeRate\Exception\ProviderNotFoundException;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use PHPUnit\Framework\TestCase;

final class ExchangeRateDataProviderFactoryTest extends TestCase
{
    private const PROVIDER_NAME = 'some-provider-name';
    private const NOT_EXISTING_PROVIDER = 'not-existing-provider';

    private ExchangeRateDataProviderFactory $object;

    private DataProviderInterface $providerMock;

    public function setUp(): void
    {
        $this->providerMock = $this->createMock(DataProviderInterface::class);

        $this->object = new ExchangeRateDataProviderFactory([
            $this->providerMock,
        ]);
    }

    public function testGetExchangeRateConverter(): void
    {
        $this->providerMock->expects($this->once())
            ->method('support')
            ->with(self::PROVIDER_NAME)
            ->willReturn(true);

        $this->assertInstanceOf(
            ExchangeRateConverterInterface::class,
            $this->object->getDataProvider(self::PROVIDER_NAME)
        );
    }

    public function testException(): void
    {
        $this->providerMock->expects($this->once())
            ->method('support')
            ->with(self::NOT_EXISTING_PROVIDER)
            ->willReturn(false);

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage('Conversion rate data provider not-existing-provider not found');

        $this->object->getDataProvider(self::NOT_EXISTING_PROVIDER);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Metrics;

use App\Event\TransactionUpdatedEvent;
use App\Metrics\TransactionUpdatedEventSubscriber;
use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;

class TransactionUpdatedEventSubscriberTest extends TestCase
{
    private TransactionUpdatedEventSubscriber $object;

    private Counter $counterMock;
    private CollectorRegistry $collectorRegistryMock;

    public function setUp(): void
    {
        $this->counterMock = $this->createMock(Counter::class);
        $this->collectorRegistryMock = $this->createMock(CollectorRegistry::class);

        $this->object = new TransactionUpdatedEventSubscriber();

        $this->object->init('unit-test', $this->collectorRegistryMock);
    }

    public function testInvoke(): void
    {
        $this->counterMock->expects($this->once())
            ->method('inc');

        $this->collectorRegistryMock->expects($this->once())
            ->method('getOrRegisterCounter')
            ->with(
                'unit-test',
                'updated_transactions',
                'provide updated transactions number'
            )->willReturn($this->counterMock);

        $this->object->__invoke(new TransactionUpdatedEvent(12));
    }
}

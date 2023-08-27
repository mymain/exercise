<?php

declare(strict_types=1);

namespace App\Tests\Unit\Metrics;

use App\Event\TransactionCreatedEvent;
use App\Metrics\TransactionCreatedEventSubscriber;
use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;

class TransactionCreatedEventSubscriberTest extends TestCase
{
    private TransactionCreatedEventSubscriber $object;

    private Counter $counterMock;
    private CollectorRegistry $collectorRegistryMock;

    public function setUp(): void
    {
        $this->counterMock = $this->createMock(Counter::class);
        $this->collectorRegistryMock = $this->createMock(CollectorRegistry::class);

        $this->object = new TransactionCreatedEventSubscriber();

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
                'created_transactions',
                'provide created transactions number'
            )->willReturn($this->counterMock);

        $this->object->__invoke(new TransactionCreatedEvent(12));
    }
}

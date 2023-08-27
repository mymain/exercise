<?php

declare(strict_types=1);

namespace App\Tests\Unit\Metrics;

use App\Event\TransactionCreatedEvent;
use App\Metrics\TransactionCreatedEventSubscriber;

class TransactionCreatedEventSubscriberTest extends AbstractSubscriberTest
{
    private TransactionCreatedEventSubscriber $object;

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new TransactionCreatedEventSubscriber();

        $this->object->init('unit-test', $this->collectorRegistryMock);
    }

    public function testInvoke(): void
    {
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

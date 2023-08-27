<?php

declare(strict_types=1);

namespace App\Tests\Unit\Metrics;

use App\Event\TransactionUpdatedEvent;
use App\Metrics\TransactionUpdatedEventSubscriber;

class TransactionUpdatedEventSubscriberTest extends AbstractSubscriberTest
{
    private TransactionUpdatedEventSubscriber $object;

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new TransactionUpdatedEventSubscriber();

        $this->object->init('unit-test', $this->collectorRegistryMock);
    }

    public function testInvoke(): void
    {
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

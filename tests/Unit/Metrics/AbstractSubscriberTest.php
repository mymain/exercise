<?php

declare(strict_types=1);

namespace App\Tests\Unit\Metrics;

use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;

abstract class AbstractSubscriberTest extends TestCase
{
    protected Counter $counterMock;
    protected CollectorRegistry $collectorRegistryMock;

    protected function setUp(): void
    {
        $this->counterMock = $this->createMock(Counter::class);
        $this->collectorRegistryMock = $this->createMock(CollectorRegistry::class);

        $this->counterMock->expects($this->once())
            ->method('inc');
    }
}

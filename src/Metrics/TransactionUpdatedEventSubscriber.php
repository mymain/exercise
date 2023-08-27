<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Event\TransactionUpdatedEvent;
use Artprima\PrometheusMetricsBundle\Metrics\MetricsCollectorInitTrait;
use Artprima\PrometheusMetricsBundle\Metrics\MetricsCollectorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
class TransactionUpdatedEventSubscriber implements MetricsCollectorInterface
{
    use MetricsCollectorInitTrait;

    public function __invoke(TransactionUpdatedEvent $event)
    {
        $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'updated_transactions',
            'provide updated transactions number'
        )->inc();
    }
}

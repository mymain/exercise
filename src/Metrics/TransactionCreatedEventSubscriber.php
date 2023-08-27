<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Event\TransactionCreatedEvent;
use Artprima\PrometheusMetricsBundle\Metrics\MetricsCollectorInitTrait;
use Artprima\PrometheusMetricsBundle\Metrics\MetricsCollectorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
class TransactionCreatedEventSubscriber implements MetricsCollectorInterface
{
    use MetricsCollectorInitTrait;

    public function __invoke(TransactionCreatedEvent $event)
    {
        $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'created_transactions',
            'provide created transactions number'
        )->inc();
    }
}

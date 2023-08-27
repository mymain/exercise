<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\TransactionUpdatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
class TransactionUpdatedEventSubscriber
{
    public function __invoke(TransactionUpdatedEvent $event)
    {
    }
}

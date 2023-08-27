<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\TransactionCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
class TransactionCreatedEventSubscriber
{
    public function __invoke(TransactionCreatedEvent $event)
    {
    }
}

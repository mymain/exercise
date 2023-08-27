<?php

declare(strict_types=1);

namespace App\Event;

class TransactionUpdatedEvent
{
    public function __construct(
        private readonly int $transactionId,
    ) {
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}

<?php

declare(strict_types=1);

namespace App\Messenger\Command;

use App\Dto\TransactionExchangeDto;

final class TransactionExchangeCommand
{
    public function __construct(
        public readonly TransactionExchangeDto $transactionExchangeDto,
        public readonly string $ip,
    ) {
    }
}

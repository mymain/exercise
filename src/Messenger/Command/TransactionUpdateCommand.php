<?php

declare(strict_types=1);

namespace App\Messenger\Command;

use App\Dto\TransactionUpdateDto;

final class TransactionUpdateCommand
{
    public function __construct(
        public readonly TransactionUpdateDto $transactionUpdateDto,
    ) {
    }
}

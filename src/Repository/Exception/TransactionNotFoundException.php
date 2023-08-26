<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use Exception;
use Throwable;

final class TransactionNotFoundException extends Exception
{
    public function __construct(int $id, $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Transaction with id %d not found', $id), $code, $previous);
    }
}

<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Exception;

use Exception;
use Throwable;

final class RateNotFoundException extends Exception
{
    public function __construct(string $currencyCode, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Conversion rate for %s not found', $currencyCode), $code, $previous);
    }
}

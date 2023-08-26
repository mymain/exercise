<?php

declare(strict_types=1);

namespace App\Trait;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

trait GetEnvelopeResult
{
    public function getEnvelopeResult(Envelope $envelope): mixed
    {
        return $envelope->last(HandledStamp::class)->getResult();
    }
}

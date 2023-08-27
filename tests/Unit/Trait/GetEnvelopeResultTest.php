<?php

declare(strict_types=1);

namespace App\Tests\Unit\Trait;

use App\Entity\Transaction;
use App\Trait\GetEnvelopeResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class GetEnvelopeResultTest extends TestCase
{
    private object $object;

    private Transaction $transaction;

    private Envelope $envelope;
    private HandledStamp $handleStamp;

    public function setUp(): void
    {
        $this->transaction = new Transaction();
        $this->handleStamp = new HandledStamp($this->transaction, 'handler-name');

        $this->object = new class {
            use GetEnvelopeResult;
        };

        $this->envelope = new Envelope($this->object, [$this->handleStamp]);
    }

    public function testGetEnvelopeResult(): void
    {
        $this->assertInstanceOf(
            Transaction::class,
            $this->object->getEnvelopeResult($this->envelope)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Test\Unit\Trait;

use App\Entity\Transaction;
use App\Trait\GetEnvelopeResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class GetEnvelopeResultTest extends TestCase
{
    private object $object;

    private Envelope $envelopeMock;
    private Transaction $transactionMock;
    private HandledStamp $handleStampMock;

    public function setUp(): void
    {
        $this->envelopeMock = $this->createMock(Envelope::class);
        $this->transactionMock = $this->createMock(Transaction::class);
        $this->handleStampMock = $this->createMock(HandledStamp::class);

        $this->object = new class {
            use GetEnvelopeResult;
        };
    }

    public function testGetEnvelopeResult(): void
    {
        $this->handleStampMock->expects($this->once())
            ->method('getResult')
            ->willReturn($this->transactionMock);

        $this->envelopeMock->expects($this->once())
            ->method('last')
            ->with(HandledStamp::class)
            ->willReturn($this->handleStampMock);

        $this->assertInstanceOf(
            Transaction::class,
            $this->object->getEnvelopeResult($this->envelopeMock)
        );
    }
}

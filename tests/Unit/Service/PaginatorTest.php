<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\PaginationDto;
use App\Service\Paginator;
use DG\BypassFinals;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;

final class PaginatorTest extends TestCase
{
    private const TOTAL_ITEMS = 100;
    private const MAX_RESULTS = 10;

    private Paginator $object;
    private PaginationDto $paginationDto;

    private Query $queryMock;
    private EntityManagerInterface $entityManagerMock;
    private Connection $connectionMock;

    public function setUp(): void
    {
        BypassFinals::enable();

        $this->queryMock = $this->createMock(Query::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->connectionMock = $this->createMock(Connection::class);

        $this->object = new Paginator();
        $this->paginationDto = new PaginationDto();
    }

    public function testPaginate(): void
    {
        $this->queryMock->expects($this->once())
            ->method('getScalarResult')
            ->willReturn([['count' => self::TOTAL_ITEMS]]);

        $this->queryMock->expects($this->once())
            ->method('getMaxResults')
            ->willReturn(self::MAX_RESULTS);

        $this->queryMock->expects($this->once())
            ->method('getParameters')
            ->willReturn($this->createMock(ArrayCollection::class));

        $this->queryMock->expects($this->once())
            ->method('getHints')
            ->willReturn([]);

        $this->queryMock->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManagerMock);

        $this->entityManagerMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects($this->once())
            ->method('getDatabasePlatform')
            ->willReturn($this->createMock(AbstractPlatform::class));


        $result = $this->object->paginate($this->queryMock, $this->paginationDto);

        $this->assertEquals(
            self::TOTAL_ITEMS,
            $result->getItems()->count(),
        );

        $this->assertEquals(
            10,
            $result->getLastPage(),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\PaginationDto;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;

final class Paginator
{
    private const ITEMS_PER_PAGE = 5;

    private PaginationDto $paginationDto;
    private OrmPaginator $items;
    private int $lastPage;

    public function paginate(
        Query $query,
        PaginationDto $paginationDto,
        int $limit = self::ITEMS_PER_PAGE,
    ): self {
        $paginator = new OrmPaginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit * ($paginationDto->page - 1))
            ->setMaxResults($limit);

        $this->lastPage = (int) ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
        $this->items = $paginator;
        $this->paginationDto = $paginationDto;

        return $this;
    }

    public function getItems(): OrmPaginator
    {
        return $this->items;
    }

    public function getPaginationDto(): PaginationDto
    {
        return $this->paginationDto;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }
}

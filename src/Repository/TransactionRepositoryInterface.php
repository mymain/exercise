<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;

interface TransactionRepositoryInterface
{
    public function findById(int $id): Transaction;
    public function getTransactionsQuery(string $sort = Criteria::DESC): Query;
    public function persist(Transaction $transaction, bool $flush = true): void;
    public function flush(): void;
}

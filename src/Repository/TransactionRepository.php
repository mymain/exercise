<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Transaction;
use App\Repository\Exception\TransactionNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

final class TransactionRepository extends ServiceEntityRepository implements TransactionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findById(int $id): Transaction
    {
        return $this->find($id) ?? throw new TransactionNotFoundException($id);
    }

    public function getTransactionsQuery(string $sort = Criteria::DESC): Query
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', $sort)
            ->getQuery();
    }

    public function persist(Transaction $transaction, bool $flush = true): void
    {
        $this->getEntityManager()->persist($transaction);

        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}

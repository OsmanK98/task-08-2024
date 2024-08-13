<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Doctrine\Repository;

use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\BankAccount\Infrastructure\Doctrine\Entity\BankAccount;
use App\BankAccount\Infrastructure\Doctrine\Entity\Transaction;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineBankAccountRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getAccountByAccountNumber(AccountNumber $accountNumber): ?BankAccount
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('ba')
            ->from(BankAccount::class, 'ba')
            ->andWhere('ba.accountNumber = :accountNumber')
            ->setParameter('accountNumber', $accountNumber->toString())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAccountByAccountId(Id $id): ?BankAccount
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('ba')
            ->from(BankAccount::class, 'ba')
            ->andWhere('ba.id = :id')
            ->setParameter('id', $id->toString())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNumberOfDayTransactionsForAccount(Id $accountId, string $transactionDate)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('count(t.id)')
            ->from(BankAccount::class, 'ba')
            ->leftJoin('ba.transactions', 't')
            ->andWhere('ba.id = :id')
            ->andWhere('t.transactionDate = :transactionDate')
            ->setParameter('id', $accountId->toString())
            ->setParameter('transactionDate', $transactionDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(BankAccount $bankAccount): void
    {
        $this->entityManager->persist($bankAccount);
        $this->entityManager->flush();
    }

    public function find(?Id $id)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('ba')
            ->from(BankAccount::class, 'ba')
            ->andWhere('ba.id = :id')
            ->setParameter('id', $id?->toString())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTransactionById(Id $id)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('t')
            ->from(Transaction::class, 't')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id->toString())
            ->getQuery()
            ->getOneOrNullResult();
    }
}

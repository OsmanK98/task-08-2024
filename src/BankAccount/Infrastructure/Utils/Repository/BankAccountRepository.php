<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Utils\Repository;

use App\BankAccount\Domain\Model\BankAccount;
use App\BankAccount\Domain\Repository\BankAccountRepositoryInterface;
use App\BankAccount\Infrastructure\Doctrine\Repository\DoctrineBankAccountRepository;
use App\BankAccount\Infrastructure\Utils\Transformer\BankAccountTransformer;
use App\Shared\Domain\Id;

class BankAccountRepository implements BankAccountRepositoryInterface
{
    public function __construct(
        private readonly DoctrineBankAccountRepository $repository,
        private readonly BankAccountTransformer $transformer,
    ) {
    }

    public function getAccountByAccountNumber(string $accountNumber): ?BankAccount
    {
        $bankAccountEntity = $this->repository->getAccountByAccountNumber($accountNumber);
        if (null === $bankAccountEntity) {
            return null;
        }

        return $this->transformer->toDomain($bankAccountEntity);
    }

    public function getAccountByAccountId(Id $accountId): ?BankAccount
    {
        $bankAccountEntity = $this->repository->getAccountByAccountId($accountId);
        if (null === $bankAccountEntity) {
            return null;
        }

        return $this->transformer->toDomain($bankAccountEntity);
    }

    public function getNumberOfDayTransactionsForAccount(Id $accountId, string $transactionDate): int
    {
        return $this->repository->getNumberOfDayTransactionsForAccount($accountId, $transactionDate);
    }

    public function save(BankAccount $senderAccount): void
    {
        $this->repository->save(
            $this->transformer->fromDomain($senderAccount)
        );
    }
}

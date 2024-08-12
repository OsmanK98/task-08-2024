<?php

namespace App\BankAccount\Domain\Repository;

use App\BankAccount\Domain\Model\BankAccount;
use App\Shared\Domain\Id;

interface BankAccountRepositoryInterface
{
    public function getAccountByAccountNumber(string $accountNumber): ?BankAccount;

    public function getAccountByAccountId(Id $accountId): ?BankAccount;

    public function getNumberOfDayTransactionsForAccount(Id $accountId, string $transactionDate): int;

    public function save(BankAccount $senderAccount): void;
}

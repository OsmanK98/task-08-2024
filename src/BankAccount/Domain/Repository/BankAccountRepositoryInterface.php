<?php

namespace App\BankAccount\Domain\Repository;

use App\BankAccount\Domain\Model\BankAccount;
use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\Shared\Domain\Id;

interface BankAccountRepositoryInterface
{
    public function getAccountByAccountNumber(AccountNumber $accountNumber): ?BankAccount;

    public function getAccountByAccountId(Id $accountId): ?BankAccount;

    public function getNumberOfDayTransactionsForAccount(Id $accountId, string $transactionDate): int;

    public function save(BankAccount $senderAccount): void;
}

<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Model;

use App\BankAccount\Domain\Exception\BalanceCannotBeNegative;
use App\BankAccount\Domain\Exception\NumberOfDailyTransactionExceeded;
use App\BankAccount\Domain\Exception\TransactionCurrencyIsNotTheSameAsAccountCurrency;
use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\BankAccount\Domain\ValueObject\Currency;
use App\BankAccount\Domain\ValueObject\Money;
use App\BankAccount\Domain\ValueObject\TransactionType;
use App\Shared\Domain\Id;

class BankAccount
{
    private Id $id;

    private Id $ownerId; // User ID from different module

    private AccountNumber $accountNumber;

    private Money $balance;

    private Currency $currency;

    /** @var array<Transaction> */
    private array $transactions = [];

    public function makeDebitTransaction(
        Id $transactionId,
        Money $amount,
        Currency $currency,
        ?BankAccount $receiverAccount,
        AccountNumber $receiverAccountNumber,
        string $transactionDate,
        int $numberOfTransaction
    ): void {
        if ($numberOfTransaction >= 3) {
            throw new NumberOfDailyTransactionExceeded();
        }

        if (!$this->currency->isEqual($currency)) {
            throw new TransactionCurrencyIsNotTheSameAsAccountCurrency();
        }

        $calculatedFee = $amount->calculateAmountByPercentage(Transaction::DEBIT_FEE_PERCENTAGE);
        $amountWithFee = $amount->add($calculatedFee);
        $this->balance = $this->balance->subtract($amountWithFee);
        if (!$this->balance->isPositive()) {
            throw new BalanceCannotBeNegative();
        }

        $this->transactions[] = new Transaction(
            $transactionId,
            $this->id,
            $receiverAccount?->id,
            $this->accountNumber,
            $receiverAccountNumber,
            $amount,
            $calculatedFee,
            $currency,
            new TransactionType(TransactionType::DEBIT),
            $transactionDate
        );
    }

    public function makeCreditTransaction(
        Id $transactionId,
        Money $amount,
        Currency $currency,
        ?BankAccount $senderAccount,
        AccountNumber $senderAccountNumber,
        string $transactionDate,
    ): void {
        if (!$this->currency->isEqual($currency)) {
            throw new TransactionCurrencyIsNotTheSameAsAccountCurrency();
        }

        $this->balance = $this->balance->add($amount);
        if (!$this->balance->isPositive()) { // It should be always positive, but to be sure we check it
            throw new BalanceCannotBeNegative();
        }

        $this->transactions[] = new Transaction(
            $transactionId,
            $senderAccount?->id,
            $this->id,
            $senderAccountNumber,
            $this->accountNumber,
            $amount,
            new Money(0),
            $currency,
            new TransactionType(TransactionType::CREDIT),
            $transactionDate
        );
    }

    public function __construct(
        Id $id,
        AccountNumber $accountNumber,
        Id $ownerId,
        Money $balance,
        Currency $currency,
        array $transactions,
    ) {
        $this->id = $id;
        $this->accountNumber = $accountNumber;
        $this->ownerId = $ownerId;
        $this->balance = $balance;
        $this->currency = $currency;
        $this->transactions = $transactions;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getOwnerId(): Id
    {
        return $this->ownerId;
    }

    public function getAccountNumber(): AccountNumber
    {
        return $this->accountNumber;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }
}

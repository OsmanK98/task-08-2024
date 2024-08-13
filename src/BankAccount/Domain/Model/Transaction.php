<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Model;

use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\BankAccount\Domain\ValueObject\Currency;
use App\BankAccount\Domain\ValueObject\Money;
use App\BankAccount\Domain\ValueObject\TransactionType;
use App\Shared\Domain\Id;

class Transaction
{
    public const DEBIT_FEE_PERCENTAGE = 0.5; // 50% -> 50

    public function __construct(
        private readonly Id $id,
        private readonly ?Id $senderAccountId,
        private readonly ?Id $receiverAccountId,
        private readonly AccountNumber $senderAccountNumber,
        private readonly AccountNumber $receiverAccountNumber,
        private readonly Money $amount,
        private readonly Money $fee,
        private readonly Currency $currency,
        private readonly TransactionType $type,
        private readonly string $transactionDate
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getSenderAccountId(): ?Id
    {
        return $this->senderAccountId;
    }

    public function getReceiverAccountId(): ?Id
    {
        return $this->receiverAccountId;
    }

    public function getSenderAccountNumber(): AccountNumber
    {
        return $this->senderAccountNumber;
    }

    public function getReceiverAccountNumber(): AccountNumber
    {
        return $this->receiverAccountNumber;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    public function getFee(): Money
    {
        return $this->fee;
    }
}

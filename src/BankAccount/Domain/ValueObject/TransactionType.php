<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\ValueObject;

class TransactionType
{
    public const DEBIT = 'debit';
    public const CREDIT = 'credit';

    public function __construct(private readonly string $type)
    {
        if (!in_array($type, [self::DEBIT, self::CREDIT])) {
            throw new \InvalidArgumentException('Invalid transaction type');
        }
    }

    public function toString(): string
    {
        return $this->type;
    }
}

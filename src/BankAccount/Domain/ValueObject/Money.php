<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\ValueObject;

class Money
{
    public function __construct(
        private readonly int $amount, // in pens, cents, etc. example 10 EUR wil be stored as 1000
    ) {
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function toInt(): int
    {
        return $this->amount;
    }

    public function subtract(Money $money): Money
    {
        return new Money($this->amount - $money->toInt());
    }

    public function calculateAmountByPercentage(float $percentage): Money
    {
        return new Money((int) ($this->amount * ($percentage / 100)));
    }

    public function add(Money $money): Money
    {
        return new Money($this->amount + $money->toInt());
    }
}

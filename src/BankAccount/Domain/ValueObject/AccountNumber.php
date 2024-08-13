<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\ValueObject;

class AccountNumber
{
    public function __construct(readonly private string $value)
    {
        // TODO validation that account number is correct
    }

    public function toString(): string
    {
        return $this->value;
    }
}

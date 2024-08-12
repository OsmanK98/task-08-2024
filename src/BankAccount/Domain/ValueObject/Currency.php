<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\ValueObject;

class Currency
{
    // example..

    public const USD = 'USD';
    public const EUR = 'EUR';
    public const GBP = 'GBP';
    public const PLN = 'PLN';

    public function __construct(
        private readonly string $currency
    ) {
        if (!in_array($currency, [self::USD, self::EUR, self::GBP, self::PLN])) {
            throw new \InvalidArgumentException('Invalid currency');
        }
    }

    public function toString(): string
    {
        return $this->currency;
    }

    public function isEqual(Currency $currency): bool
    {
        return $this->currency === $currency->currency;
    }
}

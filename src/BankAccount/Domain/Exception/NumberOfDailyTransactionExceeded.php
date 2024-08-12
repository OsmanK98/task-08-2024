<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class NumberOfDailyTransactionExceeded extends DomainException
{
}

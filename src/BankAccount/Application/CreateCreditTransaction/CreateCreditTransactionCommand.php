<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CreateCreditTransaction;

use App\Shared\Domain\Bus\Command\CommandInterface;

class CreateCreditTransactionCommand implements CommandInterface
{
    public function __construct(
        public string $senderAccountNumber,
        public string $receiverAccountNumber,
        public int $amount,
        public string $currency,
        public string $transactionDate,
    ) {
    }
}

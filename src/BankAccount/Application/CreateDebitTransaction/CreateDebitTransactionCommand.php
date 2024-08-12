<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CreateDebitTransaction;

use App\Shared\Domain\Bus\Command\CommandInterface;

class CreateDebitTransactionCommand implements CommandInterface
{
    public function __construct(
        public string $senderAccountId,
        public string $receiverAccountNumber,
        public int $amount,
        public string $currency,
        public string $userId,
        public string $transactionDate,
    ) {
    }
}

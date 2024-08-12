<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Rest\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateDebitTransactionDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $senderAccountId,
        #[Assert\NotBlank]
        public string $receiverAccountNumber,
        #[Assert\NotBlank]
        public int $amount,
        #[Assert\NotBlank]
        public string $currency,
        #[Assert\Date]
        #[Assert\NotBlank]
        public string $transactionDate,
    ) {
    }
}

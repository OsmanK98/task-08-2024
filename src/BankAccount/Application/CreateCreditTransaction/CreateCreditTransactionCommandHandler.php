<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CreateCreditTransaction;

use App\BankAccount\Domain\Repository\BankAccountRepositoryInterface;
use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\BankAccount\Domain\ValueObject\Currency;
use App\BankAccount\Domain\ValueObject\Money;
use App\Shared\Application\IdGeneratorInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;

class CreateCreditTransactionCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        readonly private BankAccountRepositoryInterface $bankAccountRepository,
        readonly private IdGeneratorInterface $idGenerator,
    ) {
    }

    public function __invoke(CreateCreditTransactionCommand $command): void
    {
        $senderAccountNumber = new AccountNumber($command->senderAccountNumber);
        $senderAccount = $this->bankAccountRepository->getAccountByAccountNumber(
            $senderAccountNumber
        );
        $receiverAccount = $this->bankAccountRepository->getAccountByAccountNumber(
            new AccountNumber($command->receiverAccountNumber)
        );
        if (null === $receiverAccount) {
            throw new \InvalidArgumentException('Receiver account not found');
        }

        $receiverAccount->makeCreditTransaction(
            $this->idGenerator->generate(),
            new Money($command->amount),
            new Currency($command->currency),
            $senderAccount, // Can be null if sender account is in different bank
            $senderAccountNumber,
            $command->transactionDate,
        );

        $this->bankAccountRepository->save($receiverAccount);
    }
}

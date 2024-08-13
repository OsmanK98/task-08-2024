<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CreateDebitTransaction;

use App\BankAccount\Domain\Repository\BankAccountRepositoryInterface;
use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\BankAccount\Domain\ValueObject\Currency;
use App\BankAccount\Domain\ValueObject\Money;
use App\Shared\Application\IdGeneratorInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Id;

class CreateDebitTransactionCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        readonly private BankAccountRepositoryInterface $bankAccountRepository,
        readonly private IdGeneratorInterface $idGenerator,
    ) {
    }

    public function __invoke(CreateDebitTransactionCommand $command): void
    {
        $receiverAccountNumber = new AccountNumber($command->receiverAccountNumber);
        $receiverAccount = $this->bankAccountRepository->getAccountByAccountNumber($receiverAccountNumber);
        $senderAccount = $this->bankAccountRepository->getAccountByAccountId(new Id($command->senderAccountId));
        if (null === $senderAccount) {
            throw new \InvalidArgumentException('Sender account not found');
        }

        // TODO validation that auth user ($command->userId) has access to his own account (senderAccountId)

        $senderAccount->makeDebitTransaction(
            $this->idGenerator->generate(),
            new Money($command->amount),
            new Currency($command->currency),
            $receiverAccount, // Can be null if receiver account is in different bank
            $receiverAccountNumber,
            $command->transactionDate,
            $this->bankAccountRepository->getNumberOfDayTransactionsForAccount(
                $senderAccount->getId(),
                $command->transactionDate
            )
        );

        $this->bankAccountRepository->save($senderAccount);

        // TODO send event/command to notify receiver account. If receiver account is in different bank,
        // send event to event bus which is comunication channel between banks
        // if receiver account is in same bank, send command to receiver account
    }
}

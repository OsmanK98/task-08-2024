<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Utils\Transformer;

use App\BankAccount\Domain\Model\BankAccount;
use App\BankAccount\Domain\Model\Transaction;
use App\BankAccount\Domain\ValueObject\Currency;
use App\BankAccount\Domain\ValueObject\Money;
use App\BankAccount\Domain\ValueObject\TransactionType;
use App\BankAccount\Infrastructure\Doctrine\Entity\BankAccount as BankAccountEntity;
use App\BankAccount\Infrastructure\Doctrine\Entity\Transaction as TransactionEntity;
use App\BankAccount\Infrastructure\Doctrine\Repository\DoctrineBankAccountRepository;
use App\Shared\Domain\Id;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BankAccountTransformer
{
    public function __construct(
        private readonly DoctrineBankAccountRepository $doctrineBankAccountRepository,
    ) {
    }

    public function toDomain(BankAccountEntity $bankAccountEntity): BankAccount
    {
        return new BankAccount(
            new Id($bankAccountEntity->getId()),
            $bankAccountEntity->getAccountNumber(),
            new Id($bankAccountEntity->getOwnerId()),
            new Money($bankAccountEntity->getBalance()),
            new Currency($bankAccountEntity->getCurrency()),
            $this->mapTransactionToDomain($bankAccountEntity->getTransactions())
        );
    }

    private function mapTransactionToDomain(Collection $transactions): array
    {
        return array_map(function (TransactionEntity $transaction) {
            return new Transaction(
                new Id($transaction->getId()),
                $transaction->getSenderBankAccount()->getId()
                    ? new Id($transaction->getSenderBankAccount()->getId())
                    : null,
                $transaction->getReceiverAccountNumber()
                    ? new Id($transaction->getReceiverAccountNumber())
                    : null,
                $transaction->getSenderAccountNumber(),
                $transaction->getReceiverAccountNumber(),
                new Money($transaction->getAmount()),
                new Money($transaction->getFee()),
                new Currency($transaction->getCurrency()),
                new TransactionType($transaction->getType()),
                $transaction->getTransactionDate()->format('Y-m-d')
            );
        }, $transactions->toArray());
    }

    public function fromDomain(BankAccount $bankAccount): BankAccountEntity
    {
        $bankAccountEntity = $this->doctrineBankAccountRepository->find($bankAccount->getId());

        if ($bankAccountEntity) {
            $bankAccountEntity->edit(
                $bankAccount->getAccountNumber(),
                $bankAccount->getOwnerId()->toString(),
                $bankAccount->getBalance()->toInt(),
                $bankAccount->getCurrency()->toString(),
                $this->mapTransactionsToEntity($bankAccount->getTransactions())
            );
        } else {
            return new BankAccountEntity(
                $bankAccount->getId()->toString(),
                $bankAccount->getAccountNumber(),
                $bankAccount->getOwnerId()->toString(),
                $bankAccount->getBalance()->toInt(),
                $bankAccount->getCurrency()->toString(),
                $this->mapTransactionsToEntity($bankAccount->getTransactions())
            );
        }

        return $bankAccountEntity;
    }

    private function mapTransactionsToEntity(array $transactions): ArrayCollection
    {
        $collection = new ArrayCollection();

        /** @var Transaction $transactionDomain */
        foreach ($transactions as $transactionDomain) {
            $transaction = $this->doctrineBankAccountRepository->findTransactionById($transactionDomain->getId());

            if ($transaction) {
                $collection->add($transaction);
            } else {
                $senderAccount = $this->doctrineBankAccountRepository->find($transactionDomain->getSenderAccountId());
                $receiverAccount = $this->doctrineBankAccountRepository->find(
                    $transactionDomain->getReceiverAccountId()
                );

                $collection->add(
                    new TransactionEntity(
                        $transactionDomain->getId()->toString(),
                        $senderAccount,
                        $receiverAccount,
                        $transactionDomain->getSenderAccountNumber(),
                        $transactionDomain->getReceiverAccountNumber(),
                        $transactionDomain->getAmount()->toInt(),
                        $transactionDomain->getFee()->toInt(),
                        $transactionDomain->getCurrency()->toString(),
                        $transactionDomain->getType()->toString(),
                        $transactionDomain->getTransactionDate()
                    )
                );
            }
        }

        return $collection;
    }
}

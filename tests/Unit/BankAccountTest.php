<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\BankAccount\Domain\Exception\NumberOfDailyTransactionExceeded;
use App\BankAccount\Domain\Exception\TransactionCurrencyIsNotTheSameAsAccountCurrency;
use App\BankAccount\Domain\Model\BankAccount;
use App\BankAccount\Domain\Model\Transaction;
use App\BankAccount\Domain\ValueObject\AccountNumber;
use App\BankAccount\Domain\ValueObject\Currency;
use App\BankAccount\Domain\ValueObject\Money;
use App\BankAccount\Domain\ValueObject\TransactionType;
use App\Shared\Domain\Id;
use PHPUnit\Framework\TestCase;

class BankAccountTest extends TestCase
{
    public function testSuccessfulCreditTransaction(): void
    {
        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeCreditTransaction(
            new Id('5'),
            new Money(500),
            new Currency('USD'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02'
        );

        $this->assertEquals(new Money(1500), $bankAccount->getBalance());
        $this->assertCount(3, $bankAccount->getTransactions());
        $transactions = $bankAccount->getTransactions();
        $lastTransaction = end($transactions);

        $this->assertEquals(new Money(500), $lastTransaction->getAmount());
        $this->assertEquals(new Currency('USD'), $lastTransaction->getCurrency());
        $this->assertEquals(new TransactionType(TransactionType::CREDIT), $lastTransaction->getType());
        $this->assertEquals('2024-05-02', $lastTransaction->getTransactionDate());
        $this->assertEquals(new AccountNumber('51251521'), $lastTransaction->getSenderAccountNumber());
        $this->assertEquals(new AccountNumber('1234567890'), $lastTransaction->getReceiverAccountNumber());
        $this->assertEquals(new Id('1'), $lastTransaction->getReceiverAccountId());
        $this->assertNull($lastTransaction->getSenderAccountId());
    }

    public function testMakeCreditTransactionCurrencyMismatch(): void
    {
        $this->expectException(TransactionCurrencyIsNotTheSameAsAccountCurrency::class);

        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeCreditTransaction(
            new Id('5'),
            new Money(500),
            new Currency('PLN'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02'
        );
    }

    public function testSuccessfulDebitTransaction(): void
    {
        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeDebitTransaction(
            new Id('5'),
            new Money(500),
            new Currency('USD'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02',
            2
        );

        $this->assertEquals(new Money(498), $bankAccount->getBalance());
        $this->assertCount(3, $bankAccount->getTransactions());
        $transactions = $bankAccount->getTransactions();
        $lastTransaction = end($transactions);

        $this->assertEquals(new Money(500), $lastTransaction->getAmount());
        $this->assertEquals(new Money(2), $lastTransaction->getFee());
        $this->assertEquals(new Currency('USD'), $lastTransaction->getCurrency());
        $this->assertEquals(new TransactionType(TransactionType::DEBIT), $lastTransaction->getType());
        $this->assertEquals('2024-05-02', $lastTransaction->getTransactionDate());
        $this->assertEquals(new AccountNumber('1234567890'), $lastTransaction->getSenderAccountNumber());
        $this->assertEquals(new AccountNumber('51251521'), $lastTransaction->getReceiverAccountNumber());
        $this->assertNull($lastTransaction->getReceiverAccountId());
        $this->assertEquals(new Id('1'), $lastTransaction->getSenderAccountId());
    }

    public function testSuccessfulDebitTransactionWhenCalculatedFeeIsZero(): void
    {
        // I assumed that, fee will be added to transaction as a cost if the calculated fee will be over then 1 cent.
        // For example 0.5% from 1 EUR is below 1 cent, so I didn't add a fee to this transaction.
        // But it easy to change that fee should be at least 1 cent for every transaction.

        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeDebitTransaction(
            new Id('5'),
            new Money(199),
            new Currency('USD'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02',
            2
        );

        $this->assertEquals(new Money(801), $bankAccount->getBalance());
        $this->assertCount(3, $bankAccount->getTransactions());
        $transactions = $bankAccount->getTransactions();
        $lastTransaction = end($transactions);

        $this->assertEquals(new Money(199), $lastTransaction->getAmount());
        $this->assertEquals(new Money(0), $lastTransaction->getFee());
        $this->assertEquals(new Currency('USD'), $lastTransaction->getCurrency());
        $this->assertEquals(new TransactionType(TransactionType::DEBIT), $lastTransaction->getType());
        $this->assertEquals('2024-05-02', $lastTransaction->getTransactionDate());
        $this->assertEquals(new AccountNumber('1234567890'), $lastTransaction->getSenderAccountNumber());
        $this->assertEquals(new AccountNumber('51251521'), $lastTransaction->getReceiverAccountNumber());
        $this->assertNull($lastTransaction->getReceiverAccountId());
        $this->assertEquals(new Id('1'), $lastTransaction->getSenderAccountId());
    }

    public function testMakeDebitTransactionCurrencyMismatch(): void
    {
        $this->expectException(TransactionCurrencyIsNotTheSameAsAccountCurrency::class);

        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeDebitTransaction(
            new Id('5'),
            new Money(500),
            new Currency('PLN'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02',
            2
        );
    }

    public function testMakeDebitTransactionBalanceCannotBeNegative(): void
    {
        $this->expectException(TransactionCurrencyIsNotTheSameAsAccountCurrency::class);

        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeDebitTransaction(
            new Id('5'),
            new Money(500),
            new Currency('PLN'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02',
            2
        );
    }

    public function testMakeDebitTransactionNumberOfDailyTransactionExceeded(): void
    {
        $this->expectException(NumberOfDailyTransactionExceeded::class);

        $bankAccount = $this->prepareBankAccountWithTransactions();
        $bankAccount->makeDebitTransaction(
            new Id('5'),
            new Money(500),
            new Currency('PLN'),
            null,
            new AccountNumber('51251521'),
            '2024-05-02',
            5
        );
    }

    private function prepareBankAccountWithTransactions(): BankAccount
    {
        $transaction1 = new Transaction(
            new Id('1'),
            new Id('2'),
            new Id('3'),
            new AccountNumber('1234567890'),
            new AccountNumber('0987654321'),
            new Money(1000),
            new Money(0),
            new Currency('USD'),
            new TransactionType(TransactionType::CREDIT),
            '2021-10-10'
        );

        $transaction2 = new Transaction(
            new Id('1'),
            new Id('2'),
            new Id('3'),
            new AccountNumber('1234567890'),
            new AccountNumber('0987654321'),
            new Money(5000),
            new Money(25),
            new Currency('USD'),
            new TransactionType(TransactionType::DEBIT),
            '2021-10-10'
        );

        return new BankAccount(
            new Id('1'),
            new AccountNumber('1234567890'),
            new Id('owner1'),
            new Money(1000),
            new Currency('USD'),
            [$transaction1, $transaction2],
        );
    }
}

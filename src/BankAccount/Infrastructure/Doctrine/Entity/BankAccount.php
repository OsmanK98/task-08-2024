<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Doctrine\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BankAccount
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(type: 'guid')]
    private string $ownerId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $accountNumber;

    #[ORM\Column(type: 'integer')]
    private int $balance;

    #[ORM\Column(type: 'string', length: 255)]
    private string $currency;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'senderBankAccount', cascade: ['persist', 'remove'])]
    private Collection $transactions;

    public function __construct(
        string $id,
        string $accountNumber,
        string $ownerId,
        int $balance,
        string $currency,
        Collection $transactions,
    ) {
        $this->id = $id;
        $this->accountNumber = $accountNumber;
        $this->ownerId = $ownerId;
        $this->balance = $balance;
        $this->currency = $currency;
        $this->transactions = $transactions;
    }

    public function edit(
        string $accountNumber,
        string $ownerId,
        int $balance,
        string $currency,
        Collection $transactions,
    ) {
        $this->accountNumber = $accountNumber;
        $this->ownerId = $ownerId;
        $this->balance = $balance;
        $this->currency = $currency;
        $this->transactions = $transactions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}

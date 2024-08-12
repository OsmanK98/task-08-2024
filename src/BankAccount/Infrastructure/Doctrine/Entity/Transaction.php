<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BankAccount $senderBankAccount;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BankAccount $receiverBankAccount;

    #[ORM\Column(type: 'string', length: 255)]
    private string $senderAccountNumber;

    #[ORM\Column(type: 'string', length: 255)]
    private string $receiverAccountNumber;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'integer')]
    private int $fee;

    #[ORM\Column(type: 'string', length: 255)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    #[ORM\Column(type: 'date')]
    private \DateTime $transactionDate;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct(
        string $id,
        ?BankAccount $senderBankAccount,
        ?BankAccount $receiverBankAccount,
        string $senderAccountNumber,
        string $receiverAccountNumber,
        int $amount,
        int $fee,
        string $currency,
        string $type,
        string $transactionDate
    ) {
        $this->id = $id;
        $this->senderBankAccount = $senderBankAccount;
        $this->receiverAccountNumber = $receiverAccountNumber;
        $this->receiverBankAccount = $receiverBankAccount;
        $this->senderAccountNumber = $senderAccountNumber;
        $this->amount = $amount;
        $this->fee = $fee;
        $this->type = $type;
        $this->currency = $currency;
        $this->transactionDate = new \DateTime($transactionDate);
        $this->createdAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSenderBankAccount(): BankAccount
    {
        return $this->senderBankAccount;
    }

    public function getReceiverAccountNumber(): string
    {
        return $this->receiverAccountNumber;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getFee(): int
    {
        return $this->fee;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getReceiverBankAccount(): ?BankAccount
    {
        return $this->receiverBankAccount;
    }

    public function getSenderAccountNumber(): string
    {
        return $this->senderAccountNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTransactionDate(): \DateTime
    {
        return $this->transactionDate;
    }
}

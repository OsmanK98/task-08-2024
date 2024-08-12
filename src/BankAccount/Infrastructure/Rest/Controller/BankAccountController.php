<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Rest\Controller;

use App\BankAccount\Application\CreateCreditTransaction\CreateCreditTransactionCommand;
use App\BankAccount\Application\CreateDebitTransaction\CreateDebitTransactionCommand;
use App\BankAccount\Infrastructure\Rest\Dto\CreateCreditTransactionDto;
use App\BankAccount\Infrastructure\Rest\Dto\CreateDebitTransactionDto;
use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\User\Shared\UserBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/bank-account')]
#[AsController]
class BankAccountController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly UserBagInterface $userBagInterface,
    ) {
    }

    #[Route('/debit-transaction', name: 'create-debit-transaction', methods: 'POST')]
    public function createDebitTransaction(#[MapRequestPayload] CreateDebitTransactionDto $createDebitTransactionDto
    ): JsonResponse {
        $userId = $this->userBagInterface->getAuthUserId();
        if (!$userId) {
            throw new AccessDeniedException('User not found');
        }

        $this->commandBus->dispatch(
            new CreateDebitTransactionCommand(
                $createDebitTransactionDto->senderAccountId,
                $createDebitTransactionDto->receiverAccountNumber,
                $createDebitTransactionDto->amount,
                $createDebitTransactionDto->currency,
                $userId->toString(),
                $createDebitTransactionDto->transactionDate,
            )
        );

        return new JsonResponse(['message' => 'Debit transaction created'], Response::HTTP_CREATED);
    }

    #[Route('/credit-transaction', name: 'create-credit-transaction', methods: 'POST')]
    public function createCreditOperation(#[MapRequestPayload] CreateCreditTransactionDto $createCreditTransactionDto
    ): JsonResponse {
        // There is no need to check if user is authenticated because this is a credit operation e.g. from another bank
        // E.g. There should be an authorization between banks

        $this->commandBus->dispatch(
            new CreateCreditTransactionCommand(
                $createCreditTransactionDto->senderAccountNumber,
                $createCreditTransactionDto->receiverAccountNumber,
                $createCreditTransactionDto->amount,
                $createCreditTransactionDto->currency,
                $createCreditTransactionDto->transactionDate,
            )
        );

        return new JsonResponse(['message' => 'Credit transaction created'], Response::HTTP_CREATED);
    }
}

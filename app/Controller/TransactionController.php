<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\TransferRequest;
use App\Resource\TransactionResource;
use App\Service\TransactionService;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Throwable;

class TransactionController
{
    public function __construct(
        protected readonly TransactionService $transactionService,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function transfer(TransferRequest $request): PsrResponseInterface
    {
        $transaction = $this->transactionService->transfer($request->validated());

        return TransactionResource::make($transaction);
    }

}

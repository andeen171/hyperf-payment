<?php

namespace App\Service;

use App\Exception\InsufficientFundsException;
use App\Exception\TransactionFailedException;
use App\Interface\AuthorizeTransferInterface;
use App\Interface\NotificationInterface;
use App\Model\Transaction;
use App\Model\Wallet;
use App\Service\Request\AuthorizeTransferService;
use App\Service\Request\NotificationService;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Coroutine\Parallel;
use Hyperf\DbConnection\Db;
use Hyperf\Retry\Annotation\Retry;
use Throwable;
use function Hyperf\Coroutine\go;

class TransactionService
{
    public function __construct(
        private readonly NotificationService      $notificationService,
        private readonly AuthorizeTransferService $authorizeTransferService
    )
    {
    }

    /**
     * @param array{
     *     value: integer,
     *     payer: integer,
     *     payee: integer,
     * } $data
     * @return Transaction
     * @throws Throwable
     */
    public function transfer(array $data): Transaction
    {
        /** @var Wallet $payerWallet */
        $payerWallet = Wallet::where('user_id', $data['payer'])->firstOrFail();

        if (($payerWallet->balance - $data['value']) < 0) {
            throw new InsufficientFundsException();
        }

        $parallel = new Parallel();
        Db::beginTransaction();
        try {
            $parallel->add(fn() => $this->authorizeTransaction($data));

            /** @var Wallet $payeeWallet */
            $payeeWallet = Wallet::where('user_id', $data['payee'])->firstOrFail();
            $transaction = $this->processTransaction($payerWallet, $payeeWallet, $data['value']);

            $parallel->wait();
        } catch (Throwable $e) {
            Db::rollBack();
            throw new TransactionFailedException($e);
        }
        Db::commit();

        go(fn() => $this->notifyTransactionSuccess($data));

        return $transaction;
    }

    private function processTransaction(Wallet $payerWallet, Wallet $payeeWallet, int $value): Transaction
    {
        $payerWallet->balance -= $value;
        $payerWallet->save();

        $payeeWallet->balance += $value;
        $payeeWallet->save();

        return Transaction::create([
            'amount' => $value,
            'payer_user_id' => $payerWallet->user_id,
            'payee_user_id' => $payeeWallet->user_id,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Retry(maxAttempts: 2, base: 200)]
    private function authorizeTransaction(array $data): array
    {
        return $this->authorizeTransferService->authorize($data);
    }

    /**
     * @throws GuzzleException
     */
    #[Retry(maxAttempts: 2, base: 200)]
    private function notifyTransactionSuccess(array $data): array
    {
        return $this->notificationService->notify($data);
    }
}
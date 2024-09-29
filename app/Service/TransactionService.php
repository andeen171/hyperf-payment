<?php

namespace App\Service;

use App\Enum\UserTypeEnum;
use App\Exception\InsufficientFundsException;
use App\Exception\ShopkeeperCannotTransferException;
use App\Exception\TransactionFailedException;
use App\Model\Transaction;
use App\Model\User;
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
        $payer = User::find($data['payer']);

        $this->canMakeTransfer($payer, $data);

        $parallel = new Parallel();
        Db::beginTransaction();
        try {
            $parallel->add(fn() => $this->authorizeTransaction($data));

            /** @var Wallet $payeeWallet */
            $payeeWallet = Wallet::where('user_id', $data['payee'])->firstOrFail();
            $transaction = $this->processTransaction($payer->wallet, $payeeWallet, $data['value']);

            $parallel->wait();
        } catch (Throwable $e) {
            Db::rollBack();
            throw new TransactionFailedException($e);
        }
        Db::commit();

        go(fn() => $this->notifyTransactionSuccess($data));

        return $transaction;
    }

    private function canMakeTransfer(User $payer, array $data): void
    {
        if ($payer->type === UserTypeEnum::SHOPKEEPER->value) {
            throw new ShopkeeperCannotTransferException();
        }

        if (($payer->wallet->balance - $data['value']) < 0) {
            throw new InsufficientFundsException();
        }
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
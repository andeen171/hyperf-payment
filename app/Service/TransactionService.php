<?php

namespace App\Service;

use App\Exception\InsufficientFundsException;
use App\Model\Transaction;
use App\Model\Wallet;
use Hyperf\DbConnection\Db;
use Throwable;

class TransactionService
{
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

        Db::beginTransaction();
        try {
            $payerWallet->balance -= $data['value'];
            $payerWallet->save();

            /** @var Wallet $payeeWallet */
            $payeeWallet = Wallet::where('user_id', $data['payee'])->firstOrFail();

            $payeeWallet->balance += $data['value'];
            $payeeWallet->save();

            $transaction = Transaction::create([
                'amount' => $data['value'],
                'payer_user_id' => $data['payer'],
                'payee_user_id' => $data['payee'],
            ]);
        } catch (Throwable $e) {
            Db::rollBack();
            throw $e;
        }
        Db::commit();

        return $transaction;
    }
}
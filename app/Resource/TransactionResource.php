<?php

namespace App\Resource;

use App\Model\Transaction;
use Hyperf\Resource\Json\JsonResource;

/**
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'payerUserId' => $this->payer_user_id,
            'payeeUserId' => $this->payee_user_id,
            'createdAt' => $this->created_at,
        ];
    }
}

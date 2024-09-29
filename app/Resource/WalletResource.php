<?php

namespace App\Resource;

use App\Model\Wallet;
use Hyperf\Resource\Json\JsonResource;

/**
 * @mixin Wallet
 */
class WalletResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'balance' => $this->balance,
            'updatedAt' => $this->updated_at,
        ];
    }
}

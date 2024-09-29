<?php

namespace App\Resource;

use App\Model\User;
use Hyperf\Resource\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'document' => $this->document,
            'wallet' => WalletResource::make($this->whenLoaded('wallet')),
            'createdAt' => $this->created_at,
        ];
    }
}

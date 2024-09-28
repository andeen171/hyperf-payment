<?php

namespace App\Resource;

use App\Model\User;
use Hyperf\Resource\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'document' => $this->document,
            'createdAt' => $this->created_at,
        ];
    }
}

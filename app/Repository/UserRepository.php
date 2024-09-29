<?php

namespace App\Repository;

use App\Enum\UserTypeEnum;
use App\Model\User;
use Hyperf\Contract\LengthAwarePaginatorInterface;

class UserRepository
{
    /**
     * @param array{
     *     page: ?integer,
     *     perPage: ?integer,
     * } $filters
     */
    public function listShopKeepers(array $filters): LengthAwarePaginatorInterface
    {
        return User::query()
            ->where('type', UserTypeEnum::SHOPKEEPER->value)
            ->paginate(
                perPage: $filters['perPage'] ?? null,
                page: $filters['page'] ?? null
            );
    }

    /**
     * @param array{
     *     page: ?integer,
     *     perPage: ?integer,
     * } $filters
     */
    public function listUsers(array $filters): LengthAwarePaginatorInterface
    {
        return User::query()
            ->where('type', UserTypeEnum::COMMON->value)
            ->paginate(
                perPage: $filters['perPage'] ?? null,
                page: $filters['page'] ?? null
            );
    }
}
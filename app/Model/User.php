<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $document
 * @property string $password
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Model
{
    protected ?string $table = 'users';

    protected array $fillable = [
        'first_name',
        'last_name',
        'email',
        'document',
        'password',
        'type'
    ];

    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payer_id', 'id');
    }

    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payee_id', 'id');
    }
}

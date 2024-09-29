<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $balance
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Wallet extends Model
{
    protected ?string $table = 'wallets';

    protected array $fillable = [
        'balance',
        'user_id'
    ];

    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'balance' => 'integer',
        'user_id' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $amount
 * @property int $payer_user_id
 * @property int $payee_user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $payer
 * @property-read User $payee
 */
class Transaction extends Model
{
    protected ?string $table = 'transactions';

    protected array $fillable = [
        'amount',
        'payer_user_id',
        'payee_user_id',
    ];

    protected array $casts = [
        'id' => 'integer',
        'amount' => 'integer',
        'payer_user_id' => 'integer',
        'payee_user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }


    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_user_id');
    }
}

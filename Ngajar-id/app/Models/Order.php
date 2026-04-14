<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $external_id
 * @property string $order_number
 * @property string $total_amount
 * @property string $status
 * @property string|null $payment_method
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'external_id',
        'order_number',
        'total_amount',
        'status',
        'payment_method',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the order.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

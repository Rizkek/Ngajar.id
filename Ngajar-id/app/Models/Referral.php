<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;

    protected $table = 'referrals';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'bonus_token',
        'status',
        'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who referred
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id', 'user_id');
    }

    /**
     * Get the user who was referred
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id', 'user_id');
    }

    /**
     * Mark as redeemed
     */
    public function markAsRedeemed(): void
    {
        $this->update([
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        // Award bonus tokens to referrer
        if ($this->referrer && $this->bonus_token > 0) {
            $this->referrer->token->increment('jumlah', $this->bonus_token);
        }
    }
}

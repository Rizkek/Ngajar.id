<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailVerification extends Model
{
    use HasFactory;

    protected $table = 'email_verifications';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Check if token is still valid
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture() && is_null($this->verified_at);
    }

    /**
     * Mark as verified
     */
    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
        $this->user->update(['email_verified_at' => now()]);
    }
}

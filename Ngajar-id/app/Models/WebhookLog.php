<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'external_id',
        'gateway',
        'payload',
        'signature',
        'status',
        'response',
        'ip_address',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];

    /**
     * Scope to get idempotent webhook.
     */
    public function scopeIdempotent($query, $externalId, $gateway)
    {
        return $query->where('external_id', $externalId)
            ->where('gateway', $gateway)
            ->where('status', 'success')
            ->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    use HasFactory;

    protected $table = 'topup';
    protected $primaryKey = 'topup_id';

    protected $fillable = [
        'user_id',
        'jumlah_token',
        'harga',
        'tanggal',
    ];

    protected $casts = [
        'jumlah_token' => 'integer',
        'harga' => 'integer',
        'tanggal' => 'datetime',
    ];

    // Relationships

    /**
     * User yang melakukan topup
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Events
    protected static function booted()
    {
        // REMOVED: Auto-add token on creation
        // Token should ONLY be added when payment is confirmed (status = success)
        // This is handled in TopupController::callback() after Xendit/Midtrans confirms payment

        // Optional: Add event for status update to 'success'
        static::updated(function ($topup) {
            // Only add tokens when status changes to 'success'
            if ($topup->isDirty('status') && $topup->status === 'success') {
                $token = Token::firstOrCreate(
                    ['user_id' => $topup->user_id],
                    ['jumlah' => 0, 'last_update' => now()]
                );

                $token->tambah($topup->jumlah_token);

                // Log to token_log
                TokenLog::create([
                    'user_id' => $topup->user_id,
                    'jumlah' => $topup->jumlah_token,
                    'aksi' => 'tambah',
                    'tipe' => 'topup',
                    'keterangan' => 'Top-up token sebesar ' . $topup->jumlah_token . ' token',
                    'tanggal' => now(),
                ]);
            }
        });
    }
}

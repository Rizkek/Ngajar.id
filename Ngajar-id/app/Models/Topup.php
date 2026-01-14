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

    // Events - Auto update token saat topup dibuat
    protected static function booted()
    {
        static::created(function ($topup) {
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
                'tanggal' => now(),
            ]);
        });
    }
}

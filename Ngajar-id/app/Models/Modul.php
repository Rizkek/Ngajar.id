<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    use HasFactory;

    protected $table = 'modul';
    protected $primaryKey = 'modul_id';

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_url',
        'tipe',
        'token_harga',
        'dibuat_oleh',
    ];

    protected $casts = [
        'token_harga' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    /**
     * Pembuat modul
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'user_id');
    }

    /**
     * User yang sudah membeli modul ini
     */
    public function pembeli()
    {
        return $this->belongsToMany(User::class, 'modul_user', 'modul_id', 'user_id')
                    ->withPivot('tanggal_beli')
                    ->withTimestamps();
    }

    /**
     * Log pembelian/penggunaan token untuk modul ini
     */
    public function tokenLogs()
    {
        return $this->hasMany(TokenLog::class, 'modul_id', 'modul_id');
    }

    // Scopes

    public function scopeGratis($query)
    {
        return $query->where('tipe', 'gratis');
    }

    public function scopePremium($query)
    {
        return $query->where('tipe', 'premium');
    }

    // Helper Methods

    public function isGratis(): bool
    {
        return $this->tipe === 'gratis';
    }

    public function isPremium(): bool
    {
        return $this->tipe === 'premium';
    }

    public function getJumlahPembeli(): int
    {
        return $this->pembeli()->count();
    }

    public function sudahDibeli(User $user): bool
    {
        return $this->pembeli()->where('user_id', $user->user_id)->exists();
    }
}

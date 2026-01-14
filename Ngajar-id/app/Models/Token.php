<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $table = 'token';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'jumlah',
        'last_update',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'last_update' => 'datetime',
    ];

    // Relationships

    /**
     * User pemilik token
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Helper Methods

    public function tambah(int $jumlah): void
    {
        $this->increment('jumlah', $jumlah);
        $this->update(['last_update' => now()]);
    }

    public function kurang(int $jumlah): bool
    {
        if ($this->jumlah < $jumlah) {
            return false;
        }

        $this->decrement('jumlah', $jumlah);
        $this->update(['last_update' => now()]);
        return true;
    }

    public function cukup(int $jumlah): bool
    {
        return $this->jumlah >= $jumlah;
    }
}

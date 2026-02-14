<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLog extends Model
{
    use HasFactory;

    protected $table = 'token_log';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'modul_id',
        'jumlah',
        'aksi',
        'tipe',
        'keterangan',
        'tanggal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal' => 'datetime',
    ];

    // Relationships

    /**
     * User yang melakukan aksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Modul terkait (jika ada)
     */
    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id', 'modul_id');
    }

    // Scopes

    public function scopeTambah($query)
    {
        return $query->where('aksi', 'tambah');
    }

    public function scopeKurang($query)
    {
        return $query->where('aksi', 'kurang');
    }
}

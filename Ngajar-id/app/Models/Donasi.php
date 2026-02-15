<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';
    protected $primaryKey = 'donasi_id';

    protected $fillable = [
        'nama',
        'email',
        'jumlah',
        'tanggal',
        'pesan',
        'status',
        'metode_pembayaran',
        'nomor_transaksi',
        'catatan_admin',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal' => 'datetime',
    ];

    // Helper Methods

    public function getTotalDonasi(): int
    {
        return self::sum('jumlah');
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal', 'desc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';

    protected $fillable = [
        'pengajar_id',
        'judul',
        'deskripsi',
        'status',
        'kategori',
        'level',
        'harga',
        'rating',
        'total_siswa',
        'durasi',
        'thumbnail',
        'harga_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi

    /**
     * Pengajar yang mengajar kelas ini
     */
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'pengajar_id', 'user_id');
    }

    /**
     * Materi dalam kelas ini
     */
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Peserta/murid yang terdaftar di kelas ini
     */
    public function peserta()
    {
        return $this->belongsToMany(User::class, 'kelas_peserta', 'kelas_id', 'siswa_id')
            ->withPivot('tanggal_daftar')
            ->withTimestamps();
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class, 'kelas_id');
    }

    public function diskusi()
    {
        return $this->hasMany(DiskusiKelas::class, 'kelas_id')->whereNull('parent_id');
    }

    public function catatan()
    {
        return $this->hasMany(CatatanUser::class, 'kelas_id');
    }

    // Scope

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    // Helper method

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function getJumlahPeserta(): int
    {
        return $this->peserta()->count();
    }

    public function getJumlahMateri(): int
    {
        return $this->materi()->count();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';
    protected $primaryKey = 'materi_id';

    protected $fillable = [
        'kelas_id',
        'judul',
        'tipe',
        'file_url',
        'deskripsi',
        'is_premium',
        'harga_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_premium' => 'boolean',
    ];

    // Relationships

    /**
     * Kelas yang memiliki materi ini
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    /**
     * User yang sudah membuka materi ini
     */
    public function aksesUsers()
    {
        return $this->belongsToMany(User::class, 'materi_akses', 'materi_id', 'user_id')
            ->withPivot('unlocked_at');
    }

    /**
     * Cek apakah materi ini sudah terbuka untuk user tertentu
     */
    public function isUnlockedBy($user)
    {
        // 1. Jika materi gratis, terbuka untuk semua
        if (!$this->is_premium)
            return true;

        // 2. Jika user tidak login, tertutup
        if (!$user)
            return false;

        // 3. Jika user pemilik materi (Pengajar), terbuka
        if ($user->user_id == $this->kelas->pengajar_id)
            return true;

        // 4. Jika user Admin, terbuka
        if ($user->isAdmin())
            return true;

        // 5. Jika user punya beasiswa, semua materi terbuka (Gratis)
        if ($user->hasBeasiswa())
            return true;

        // 6. Cek apakah sudah dibeli (ada di tabel pivot)
        return $this->aksesUsers()->where('materi_akses.user_id', $user->user_id)->exists();
    }

    // Scopes

    public function scopeVideo($query)
    {
        return $query->where('tipe', 'video');
    }

    public function scopePdf($query)
    {
        return $query->where('tipe', 'pdf');
    }

    public function scopeSoal($query)
    {
        return $query->where('tipe', 'soal');
    }

    // Helper Methods

    public function isVideo(): bool
    {
        return $this->tipe === 'video';
    }

    public function isPdf(): bool
    {
        return $this->tipe === 'pdf';
    }

    public function isSoal(): bool
    {
        return $this->tipe === 'soal';
    }

    public function getFileExtension(): string
    {
        return pathinfo($this->file_url, PATHINFO_EXTENSION);
    }
}

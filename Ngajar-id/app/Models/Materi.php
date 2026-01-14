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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    /**
     * Kelas yang memiliki materi ini
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
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

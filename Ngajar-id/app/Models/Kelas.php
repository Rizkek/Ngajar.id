<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Kelas (Class/Course) Model
 *
 * Database attributes
 * @property int $kelas_id Class ID (Primary Key)
 * @property int $pengajar_id Instructor user ID (Foreign Key)
 * @property string $judul Class title
 * @property string $deskripsi Class description
 * @property string $status Class status (draft, published, archived)
 * @property string $kategori Class category
 * @property string $level Class difficulty level (beginner, intermediate, advanced)
 * @property float $harga Class price in currency
 * @property float $rating Average class rating (1-5)
 * @property int $total_siswa Total students enrolled
 * @property int $durasi Total duration in hours
 * @property string $thumbnail Thumbnail image filename
 * @property float $harga_token Price in platform tokens
 * @property \Illuminate\Support\Carbon $created_at Record creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Record update timestamp
 *
 * Relations
 * @property-read \App\Models\User $pengajar Instructor relationship
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Materi[] $materi Materials/lessons in this class
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $peserta Students enrolled in this class
 */
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

    /**
     * Eager load instructor info to avoid N+1
     */
    public function scopeWithInstructor($query)
    {
        return $query->with('pengajar:user_id,name,avatar');
    }

    /**
     * Eager load materials to avoid N+1
     */
    public function scopeWithMaterials($query)
    {
        return $query->with('materi:materi_id,kelas_id,judul,created_at');
    }

    /**
     * Eager load student count to avoid N+1
     */
    public function scopeWithStudentCount($query)
    {
        return $query->withCount('peserta');
    }

    /**
     * Eager load review data
     */
    public function scopeWithRating($query)
    {
        return $query->withAvg('ulasans', 'rating');
    }

    /**
     * Filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('kategori', $category);
    }

    /**
     * Filter by instructor
     */
    public function scopeByInstructor($query, int $instructorId)
    {
        return $query->where('pengajar_id', $instructorId);
    }

    /**
     * Get only published classes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'aktif')->where('hidden', false);
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    use HasFactory;

    protected $table = 'learning_paths';
    protected $primaryKey = 'path_id';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'level',
        'estimated_hours',
        'thumbnail',
        'created_by',
        'is_active',
        'is_free',
        'harga_token',
        'total_enrolled',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'estimated_hours' => 'integer',
        'total_enrolled' => 'integer',
    ];

    // Relationships

    /**
     * Pengajar yang membuat path ini
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Kelas-kelas dalam path ini (dengan urutan)
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'learning_path_kelas', 'path_id', 'kelas_id')
            ->withPivot('urutan', 'is_required')
            ->withTimestamps()
            ->orderBy('learning_path_kelas.urutan');
    }

    /**
     * Users yang enrolled di path ini
     */
    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'user_path_progress', 'path_id', 'user_id')
            ->withPivot('progress_percentage', 'started_at', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Progress records untuk path ini
     */
    public function progressRecords()
    {
        return $this->hasMany(UserPathProgress::class, 'path_id', 'path_id');
    }

    // Helper Methods

    /**
     * Cek apakah user sudah enroll di path ini
     */
    public function isEnrolledBy($user)
    {
        if (!$user)
            return false;

        return $this->enrolledUsers()->where('user_path_progress.user_id', $user->user_id)->exists();
    }

    /**
     * Get progress user di path ini
     */
    public function getProgressFor($user)
    {
        if (!$user)
            return null;

        return UserPathProgress::where('user_id', $user->user_id)
            ->where('path_id', $this->path_id)
            ->first();
    }

    /**
     * Hitung total kelas dalam path
     */
    public function getTotalKelasAttribute()
    {
        return $this->kelas()->count();
    }

    /**
     * Get kelas berikutnya yang harus diambil user
     */
    public function getNextKelasFor($user)
    {
        $progress = $this->getProgressFor($user);
        if (!$progress)
            return $this->kelas()->first();

        $completedIds = json_decode($progress->completed_kelas ?? '[]', true);

        return $this->kelas()
            ->whereNotIn('kelas.kelas_id', $completedIds)
            ->first();
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('total_enrolled', 'desc');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_free', false);
    }
}

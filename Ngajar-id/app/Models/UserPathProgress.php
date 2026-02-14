<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPathProgress extends Model
{
    use HasFactory;

    protected $table = 'user_path_progress';

    protected $fillable = [
        'user_id',
        'path_id',
        'current_kelas_id',
        'completed_kelas',
        'progress_percentage',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'completed_kelas' => 'array',
        'progress_percentage' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function learningPath()
    {
        return $this->belongsTo(LearningPath::class, 'path_id', 'path_id');
    }

    public function currentKelas()
    {
        return $this->belongsTo(Kelas::class, 'current_kelas_id', 'kelas_id');
    }

    // Helper Methods

    /**
     * Tandai kelas sebagai selesai
     */
    public function markKelasCompleted($kelasId)
    {
        $completed = $this->completed_kelas ?? [];

        if (!in_array($kelasId, $completed)) {
            $completed[] = $kelasId;
            $this->completed_kelas = $completed;

            // Update progress percentage
            $totalKelas = $this->learningPath->kelas()->count();
            $this->progress_percentage = $totalKelas > 0
                ? (int) ((count($completed) / $totalKelas) * 100)
                : 0;

            // Jika semua kelas selesai, set completed_at
            if ($this->progress_percentage >= 100) {
                $this->completed_at = now();
            }

            $this->save();
        }
    }

    /**
     * Set kelas yang sedang diambil
     */
    public function setCurrentKelas($kelasId)
    {
        $this->current_kelas_id = $kelasId;

        // Set started_at jika belum ada
        if (!$this->started_at) {
            $this->started_at = now();
        }

        $this->save();
    }

    /**
     * Cek apakah kelas sudah selesai
     */
    public function isKelasCompleted($kelasId)
    {
        $completed = $this->completed_kelas ?? [];
        return in_array($kelasId, $completed);
    }

    /**
     * Cek apakah path sudah selesai
     */
    public function isCompleted()
    {
        return $this->completed_at !== null;
    }

    /**
     * Get kelas berikutnya yang harus diambil
     */
    public function getNextKelas()
    {
        $completedIds = $this->completed_kelas ?? [];

        return $this->learningPath->kelas()
            ->whereNotIn('kelas.kelas_id', $completedIds)
            ->first();
    }
}

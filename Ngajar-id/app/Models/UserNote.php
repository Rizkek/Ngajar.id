<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNote extends Model
{
    use HasFactory;

    protected $table = 'catatan_users';

    protected $fillable = [
        'user_id',
        'kelas_id',
        'materi_id',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Course::class, 'kelas_id');
    }

    public function materi()
    {
        return $this->belongsTo(Lesson::class, 'materi_id');
    }
}



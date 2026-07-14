<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDiscussion extends Model
{
    use HasFactory;

    protected $table = 'diskusi_kelas';

    protected $fillable = [
        'user_id',
        'kelas_id',
        'parent_id',
        'konten',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Course::class, 'kelas_id');
    }

    public function parent()
    {
        return $this->belongsTo(CourseDiscussion::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CourseDiscussion::class, 'parent_id');
    }
}



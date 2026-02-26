<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    protected $table = 'broadcast_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'message',
        'recipient_type',
        'kelas_id',
        'action_url',
        'priority',
        'recipient_count',
        'sent_by',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'sent_by', 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }
}

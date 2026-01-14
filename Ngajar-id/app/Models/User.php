<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships

    /**
     * Kelas yang diajar (untuk pengajar)
     */
    public function kelasAjar()
    {
        return $this->hasMany(Kelas::class, 'pengajar_id', 'user_id');
    }

    /**
     * Kelas yang diikuti (untuk murid)
     */
    public function kelasIkuti()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_peserta', 'siswa_id', 'kelas_id')
                    ->withPivot('tanggal_daftar')
                    ->withTimestamps();
    }

    /**
     * Modul yang dibuat
     */
    public function modulDibuat()
    {
        return $this->hasMany(Modul::class, 'dibuat_oleh', 'user_id');
    }

    /**
     * Modul yang dibeli/dimiliki
     */
    public function modulDimiliki()
    {
        return $this->belongsToMany(Modul::class, 'modul_user', 'user_id', 'modul_id')
                    ->withPivot('tanggal_beli')
                    ->withTimestamps();
    }

    /**
     * Token user
     */
    public function token()
    {
        return $this->hasOne(Token::class, 'user_id', 'user_id');
    }

    /**
     * Riwayat topup
     */
    public function topups()
    {
        return $this->hasMany(Topup::class, 'user_id', 'user_id');
    }

    /**
     * Riwayat token log
     */
    public function tokenLogs()
    {
        return $this->hasMany(TokenLog::class, 'user_id', 'user_id');
    }

    // Scopes

    public function scopeMurid($query)
    {
        return $query->where('role', 'murid');
    }

    public function scopePengajar($query)
    {
        return $query->where('role', 'pengajar');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helper Methods

    public function isMurid(): bool
    {
        return $this->role === 'murid';
    }

    public function isPengajar(): bool
    {
        return $this->role === 'pengajar';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function getSaldoToken(): int
    {
        return $this->token?->jumlah ?? 0;
    }
}

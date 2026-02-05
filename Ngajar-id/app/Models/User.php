<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * @property int $user_id
     * @property string $name
     * @property string $email
     * @property string $password
     * @property string $role
     * @property string $status
     * @property string|null $google_id
     * @property string|null $avatar
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     */

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * Atribut yang bisa diisi secara massal
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'google_id',
        'avatar',
        'xp',
        'level',
        'achievements',
    ];

    /**
     * Atribut yang di-hide untuk serialisasi
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang di-cast
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'achievements' => 'array',
        ];
    }

    // Relasi

    /**
     * Relasi: Daftar kelas yang dibuat/diajar oleh user ini (Khusus Pengajar)
     */
    public function kelasAjar()
    {
        return $this->hasMany(Kelas::class, 'pengajar_id', 'user_id');
    }

    /**
     * Relasi: Daftar kelas yang diikuti peserta (Khusus Murid)
     * Menggunakan tabel pivot 'kelas_peserta'
     */
    public function kelasIkuti()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_peserta', 'siswa_id', 'kelas_id')
            ->withPivot('tanggal_daftar')
            ->withTimestamps();
    }

    /**
     * Relasi: Modul premium yang dibuat oleh user (Khusus Pengajar)
     */
    public function modulDibuat()
    {
        return $this->hasMany(Modul::class, 'dibuat_oleh', 'user_id');
    }

    /**
     * Relasi: Modul premium yang sudah dibeli oleh user (Khusus Murid)
     */
    public function modulDimiliki()
    {
        return $this->belongsToMany(Modul::class, 'modul_user', 'user_id', 'modul_id')
            ->withPivot('tanggal_beli')
            ->withTimestamps();
    }

    /**
     * Relasi: Dompet Token yang dimiliki user
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
     * Relasi: Riwayat penggunaan/perolehan token
     */
    public function tokenLogs()
    {
        return $this->hasMany(TokenLog::class, 'user_id', 'user_id');
    }

    // --- Scope Query (Penyaring Data) ---

    // Filter user dengan role 'murid'
    public function scopeMurid($query)
    {
        return $query->where('role', 'murid');
    }

    // Filter user dengan role 'pengajar'
    public function scopePengajar($query)
    {
        return $query->where('role', 'pengajar');
    }

    // Filter user dengan role 'admin'
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    // Filter user status 'aktif'
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helper method

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

    /**
     * Get User Rank Title based on Level
     */
    public function getRankTitleAttribute(): string
    {
        if ($this->level >= 50)
            return 'Grandmaster';
        if ($this->level >= 20)
            return 'Expert';
        if ($this->level >= 10)
            return 'Intermediate';
        if ($this->level >= 5)
            return 'Junior';
        return 'Novice';
    }
}

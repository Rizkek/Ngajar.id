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
    public $incrementing = true;

    /**
     * Database attributes
     *
     * @property int $user_id User ID (Primary Key)
     * @property string $name User full name
     * @property string $email User email address
     * @property string $password Hashed password
     * @property string $role User role (admin, pengajar, murid)
     * @property string $status User status (aktif, pending, banned)
     * @property string|null $google_id Google ID for OAuth
     * @property string|null $avatar Avatar filename
     * @property string|null $avatar_path Avatar storage path
     * @property string|null $phone User phone number
     * @property string|null $remember_token Remember me token
     * @property int|null $xp Experience points
     * @property int|null $level User level
     * @property string|null $bio User biography
     * @property array|null $achievements User achievements
     * @property bool $is_beasiswa Scholarship status
     * @property string|null $referral_code Referral code
     * @property bool $email_notifications Email notification preference
     * @property \Illuminate\Support\Carbon|null $email_verified_at Email verification date
     * @property \Illuminate\Support\Carbon $created_at Record creation time
     * @property \Illuminate\Support\Carbon $updated_at Record update time
     *
     * Relations
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Kelas[] $kelasIkuti Classes user has enrolled in
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Kelas[] $kelasAjar Classes user is teaching
     *
     * Attributes
     * @property-read string $email_verified Email verification status
     * @property-read string $avatar_url Avatar URL
     */

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
        'avatar_path',
        'bio',
        'phone',
        'referral_code',
        'email_notifications',
        'xp',
        'level',
        'achievements',
        'is_beasiswa',
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
            'is_beasiswa' => 'boolean',
            'email_notifications' => 'boolean',
        ];
    }

    // ... existing relations ...

    public function hasBeasiswa(): bool
    {
        return $this->is_beasiswa ?? false;
    }

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
     * Learning Paths yang dibuat oleh user (Khusus Pengajar)
     */
    public function learningPathsCreated()
    {
        return $this->hasMany(LearningPath::class, 'created_by', 'user_id');
    }

    /**
     * Learning Paths yang diikuti oleh user (Khusus Murid)
     */
    public function learningPathsEnrolled()
    {
        return $this->belongsToMany(LearningPath::class, 'user_path_progress', 'user_id', 'path_id')
            ->withPivot('progress_percentage', 'started_at', 'completed_at', 'current_kelas_id')
            ->withTimestamps();
    }

    /**
     * Progress records di learning paths
     */
    public function pathProgress()
    {
        return $this->hasMany(UserPathProgress::class, 'user_id', 'user_id');
    }

    /**
     * Relasi: Riwayat penggunaan/perolehan token
     */
    public function tokenLogs()
    {
        return $this->hasMany(TokenLog::class, 'user_id', 'user_id');
    }

    /**
     * Relasi: Referrals yang dibuat user (sebagai referrer)
     */
    public function referralsAsReferrer()
    {
        return $this->hasMany(Referral::class, 'referrer_id', 'user_id');
    }

    /**
     * Relasi: Referral yang mereferensikan user ini
     */
    public function referralAsReferred()
    {
        return $this->hasOne(Referral::class, 'referred_id', 'user_id');
    }

    /**
     * Relasi: Email verification records
     */
    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class, 'user_id', 'user_id');
    }

    /**
     * Get the latest unverified email verification
     */
    public function latestUnverifiedEmail()
    {
        return $this->emailVerifications()
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
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

    /**
     * Eager load enrolled classes to avoid N+1
     */
    public function scopeWithEnrolledClasses($query)
    {
        return $query->with('kelasIkuti:kelas_id,judul,kategori,pengajar_id');
    }

    /**
     * Eager load taught classes (for teachers)
     */
    public function scopeWithTaughtClasses($query)
    {
        return $query->with('kelasAjar:kelas_id,judul,status');
    }

    /**
     * Eager load token info
     */
    public function scopeWithToken($query)
    {
        return $query->with('token:token_id,user_id,jumlah');
    }

    /**
     * Get with all relationships
     */
    public function scopeWithRelations($query)
    {
        return $query->with([
            'kelasIkuti:kelas_id,judul,kategori',
            'token:token_id,jumlah',
            'topups:topup_id,user_id,jumlah',
        ]);
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


}

<?php

namespace App\Services\Teacher;

use App\Models\TokenLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EarningService
{
    /**
     * Get earning statistics for a teacher (lifetime, this month, past 6 months).
     *
     * @param User $teacher
     * @return array
     */
    public function getEarningStats(User $teacher): array
    {
        // Calculate by month for past 6 months
        $monthlyStats = DB::table('token_logs')
            ->where('user_id', $teacher->user_id)
            ->where('tipe', 'pendapatan')
            ->where('tanggal', '>=', now()->subMonths(6))
            ->selectRaw('DATE_TRUNC(\'month\', tanggal) as month, SUM(jumlah) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $totalLifetime = TokenLog::where('user_id', $teacher->user_id)
            ->where('tipe', 'pendapatan')
            ->sum('jumlah') ?? 0;

        $thisMonth = TokenLog::where('user_id', $teacher->user_id)
            ->where('tipe', 'pendapatan')
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('jumlah') ?? 0;

        return [
            'monthly_stats' => $monthlyStats,
            'total_lifetime' => $totalLifetime,
            'this_month' => $thisMonth
        ];
    }

    /**
     * Get paginated earning history for a teacher.
     *
     * @param User $teacher
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getEarningHistory(User $teacher, int $perPage = 15)
    {
        return TokenLog::where('user_id', $teacher->user_id)
            ->where('tipe', 'pendapatan')
            ->orderBy('tanggal', 'desc')
            ->paginate($perPage);
    }

    /**
     * Helper to distribute revenue from a token purchase.
     * 95% to teacher, 5% to platform.
     * 
     * @param int $teacherId
     * @param int $amount
     * @param string $keterangan
     * @return void
     */
    public function distributeRevenue(int $teacherId, int $amount, string $keterangan)
    {
        $platformFee = round($amount * 0.05); // 5% fee
        $teacherEarning = $amount - $platformFee;

        // Add earning log for teacher
        TokenLog::create([
            'user_id' => $teacherId,
            'jumlah' => $teacherEarning,
            'aksi' => 'tambah',
            'tipe' => 'pendapatan',
            'keterangan' => $keterangan . " (Potongan Platform 5%)",
            'tanggal' => now(),
        ]);

        // Add platform fee log (optional, usually admin user id or dedicated platform account)
        // TokenLog::create([
        //     'user_id' => 1, // assuming Admin ID is 1
        //     'jumlah' => $platformFee,
        //     'aksi' => 'tambah',
        //     'tipe' => 'komisi',
        //     'keterangan' => "Komisi 5% dari: " . $keterangan,
        //     'tanggal' => now(),
        // ]);
    }
}

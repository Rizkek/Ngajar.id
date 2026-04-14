@extends('layouts.dashboard-api')

@section('title', 'Dashboard Murid')
@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 id="greeting" class="text-3xl font-bold text-slate-900">Halo, User! 👋</h1>
        <p class="text-slate-600 mt-1">"Pendidikan adalah senjata paling ampuh untuk mengubah dunia." - Nelson Mandela</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Level Card -->
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-indigo-200 text-sm font-medium uppercase">Level Saat Ini</p>
                    <h2 id="user-level" class="text-4xl font-black mt-1">1</h2>
                </div>
                <span class="material-symbols-rounded text-yellow-300 text-4xl">workspace_premium</span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span id="xp-current" class="font-bold">0 XP</span>
                    <span id="xp-target" class="text-indigo-300">Target: 0 XP</span>
                </div>
                <div class="w-full bg-indigo-900/50 rounded-full h-3">
                    <div id="xp-progress" class="bg-yellow-400 h-3 rounded-full transition-all duration-1000" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-emerald-200 text-sm font-medium uppercase">Kursus Diambil</p>
                    <h2 id="courses-count" class="text-4xl font-black mt-1">0</h2>
                </div>
                <span class="material-symbols-rounded text-blue-300 text-4xl">menu_book</span>
            </div>
            <p class="text-sm text-emerald-200">Lanjutkan belajar sekarang</p>
            <a href="/student/my-courses" class="inline-block mt-3 text-sm font-semibold hover:underline">Lihat Kursus →</a>
        </div>

        <!-- Certificates Card -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-purple-200 text-sm font-medium uppercase">Sertifikat</p>
                    <h2 id="certificates-count" class="text-4xl font-black mt-1">0</h2>
                </div>
                <span class="material-symbols-rounded text-amber-300 text-4xl">school</span>
            </div>
            <p class="text-sm text-purple-200">Selesaikan kursus untuk dapatkan</p>
            <a href="/student/certificates" class="inline-block mt-3 text-sm font-semibold hover:underline">Lihat Sertifikat →</a>
        </div>
    </div>

    <!-- Recently Started Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Lanjutkan Belajar</h2>
        <div id="recent-courses" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="text-center py-8 text-slate-500">
                <span class="material-symbols-rounded text-4xl text-slate-300">hourglass_empty</span>
                <p class="mt-2">Memuat...</p>
            </div>
        </div>
    </div>

    <!-- Leaderboard Preview -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-slate-900">Top Learners</h2>
            <a href="/leaderboard" class="text-teal-600 hover:underline text-sm font-semibold">Lihat Semua →</a>
        </div>
        <div id="leaderboard-preview" class="space-y-3">
            <div class="text-center py-8 text-slate-500">
                <p>Memuat...</p>
            </div>
        </div>
    </div>

    <!-- Recommended Courses -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Rekomendasi Untuk Anda</h2>
        <div id="recommended-courses" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center py-8 text-slate-500">
                <p>Memuat...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(async function loadDashboard() {
    try {
        // Get current user
        const userRes = await api.getCurrentUser();
        if (!userRes.success) throw new Error('Failed to load user');

        const user = userRes.data;
        document.getElementById('greeting').textContent = `Halo, ${user.name}! 👋`;

        // Update user level and XP
        const level = user.level || 1;
        const xp = user.xp || 0;
        const xpPerLevel = 1000; // Base XP needed per level
        const nextLevelXp = level * xpPerLevel;

        document.getElementById('user-level').textContent = level;
        document.getElementById('xp-current').textContent = `${xp} XP`;
        document.getElementById('xp-target').textContent = `Target: ${nextLevelXp} XP`;

        const progressPercent = Math.min((xp / nextLevelXp) * 100, 100);
        document.getElementById('xp-progress').style.width = `${progressPercent}%`;

        // Get my courses
        const coursesRes = await api.getMyCourses(1);
        if (coursesRes.success) {
            const count = coursesRes.data?.pagination?.total || 0;
            document.getElementById('courses-count').textContent = count;

            // Show recent courses
            if (coursesRes.data?.data && coursesRes.data.data.length > 0) {
                const recentCoursesHtml = coursesRes.data.data.slice(0, 3).map(course => `
                    <div class="bg-slate-50 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="window.location.href='/student/course/${course.kelas_id}'">
                        <div class="aspect-video bg-gradient-to-br from-teal-400 to-teal-600 rounded-md mb-3 flex items-center justify-center text-white">
                            <span class="material-symbols-rounded text-4xl">book</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 text-sm line-clamp-2">${course.judul}</h3>
                        <div class="mt-2 bg-white rounded-full h-2 overflow-hidden">
                            <div class="bg-teal-500 h-full transition-all" style="width: ${course.progress || 0}%"></div>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">${course.progress || 0}% Selesai</p>
                    </div>
                `).join('');
                document.getElementById('recent-courses').innerHTML = recentCoursesHtml;
            }
        }

        // Get certificates
        const certsRes = await api.getMyCertificates(1);
        if (certsRes.success && certsRes.data?.pagination?.total) {
            document.getElementById('certificates-count').textContent = certsRes.data.pagination.total;
        }

        // Get leaderboard preview
        const leaderboardRes = await api.getGlobalLeaderboard(1);
        if (leaderboardRes.success && leaderboardRes.data?.data) {
            const leaderboardHtml = leaderboardRes.data.data.slice(0, 3).map((item, idx) => `
                <div class="flex items-center gap-3 p-3 rounded-lg ${idx === 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50'}">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br ${['from-yellow-400 to-yellow-600', 'from-slate-400 to-slate-600', 'from-amber-700 to-amber-900'][idx]} flex items-center justify-center text-white font-bold text-sm">
                        ${idx + 1}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 text-sm truncate">${item.name}</p>
                        <p class="text-xs text-slate-600">Level ${item.level} • ${item.xp} XP</p>
                    </div>
                </div>
            `).join('');
            document.getElementById('leaderboard-preview').innerHTML = leaderboardHtml;
        }

        // Get trending courses as recommendations
        const trendingRes = await api.getTrendingCourses(8);
        if (trendingRes.success && trendingRes.data?.data) {
            const recommendedHtml = trendingRes.data.data.map(course => `
                <div class="bg-white border border-slate-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='/student/course/${course.kelas_id}'">
                    <div class="aspect-video bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white">
                        <span class="material-symbols-rounded text-4xl">auto_stories</span>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-slate-900 text-sm line-clamp-2">${course.judul}</h3>
                        <p class="text-xs text-slate-600 mt-1">oleh ${course.instructor_name}</p>
                        <p class="text-sm font-bold text-teal-600 mt-2">Rp ${course.harga?.toLocaleString('id-ID') || '0'}</p>
                    </div>
                </div>
            `).join('');
            document.getElementById('recommended-courses').innerHTML = recommendedHtml;
        }

    } catch (error) {
        console.error('Dashboard load error:', error);
        showToast('Gagal memuat data dashboard', 'error');
    }
})();
</script>
@endsection

@extends('layouts.dashboard-api')

@section('title', 'Leaderboard')
@section('header_title', 'Leaderboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- My Rank Card -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-teal-100 text-sm">Peringkat Anda</p>
                    <p id="my-rank" class="text-4xl font-bold">-</p>
                </div>
                <span class="material-symbols-rounded text-4xl">trending_up</span>
            </div>
            <p id="my-rank-info" class="text-teal-100 text-sm">Memuat...</p>
        </div>

        <!-- My Level Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-purple-100 text-sm">Level Anda</p>
                    <p id="my-level" class="text-4xl font-bold">-</p>
                </div>
                <span class="material-symbols-rounded text-4xl">star</span>
            </div>
            <div id="level-progress" class="mt-2">
                <div class="h-2 bg-purple-400/50 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-300 transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-purple-100 text-xs mt-2" id="level-text">Memuat...</p>
            </div>
        </div>

        <!-- Total XP Card -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-amber-100 text-sm">Total XP</p>
                    <p id="total-xp" class="text-4xl font-bold">-</p>
                </div>
                <span class="material-symbols-rounded text-4xl">flash_on</span>
            </div>
            <p class="text-amber-100 text-sm">Terus tingkatkan level</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-200">
        <button id="tab-global" onclick="leaderboard.switchTab('global')"
            class="px-6 py-3 font-medium text-teal-600 border-b-2 border-teal-600 transition-colors">
            Global
        </button>
        <button id="tab-achievements" onclick="leaderboard.switchTab('achievements')"
            class="px-6 py-3 font-medium text-slate-600 border-b-2 border-transparent hover:text-slate-900 transition-colors">
            Achievements
        </button>
    </div>

    <!-- Global Leaderboard Tab -->
    <div id="global-section" class="space-y-4">
        <!-- Search & Filter -->
        <div class="flex gap-4 flex-wrap">
            <input type="text" id="search-leaderboard" placeholder="Cari peserta..."
                @keyup.debounce="leaderboard.filterLeaderboard()"
                class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 flex-1 max-w-sm">
            <select id="filter-range" @change="leaderboard.loadGlobalLeaderboard()"
                class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="all">Semua Waktu</option>
                <option value="week">Minggu Ini</option>
                <option value="month">Bulan Ini</option>
            </select>
        </div>

        <!-- Leaderboard Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-bold text-slate-900">Peringkat</th>
                            <th class="px-6 py-3 text-left text-sm font-bold text-slate-900">Nama</th>
                            <th class="px-6 py-3 text-center text-sm font-bold text-slate-900">Level</th>
                            <th class="px-6 py-3 text-right text-sm font-bold text-slate-900">XP</th>
                            <th class="px-6 py-3 text-right text-sm font-bold text-slate-900">Kursus</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboard-body">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce"></div>
                                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex items-center justify-center gap-2">
            <!-- Will be populated -->
        </div>
    </div>

    <!-- Achievements Tab -->
    <div id="achievements-section" class="hidden space-y-4">
        <!-- Achievements Grid -->
        <div id="achievements-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="text-center py-12 col-span-full">
                <div class="inline-flex items-center space-x-2">
                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce"></div>
                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
class LeaderboardView {
    constructor() {
        this.currentTab = 'global';
        this.currentPage = 1;
        this.perPage = 20;
        this.allLeaderboard = [];
        this.filteredLeaderboard = [];
        this.myRank = null;
        this.achievements = [];
    }

    async load() {
        try {
            // Load user rank
            const rankRes = await api.getMyRank();
            this.myRank = rankRes.data || {};
            this.renderMyStats();

            // Load global leaderboard
            await this.loadGlobalLeaderboard();

            // Load achievements
            await this.loadAchievements();
        } catch (error) {
            console.error('Load error:', error);
            showToast('Gagal memuat data', 'error');
        }
    }

    renderMyStats() {
        if (!this.myRank) return;

        // Rank
        document.getElementById('my-rank').textContent = this.myRank.rank || '-';
        const rankInfo = this.myRank.rank ?
            `${this.myRank.total_users} peserta terdaftar` :
            'Belum ada data';
        document.getElementById('my-rank-info').textContent = rankInfo;

        // Level
        document.getElementById('my-level').textContent = this.myRank.current_level || 0;
        document.getElementById('total-xp').textContent = (this.myRank.total_xp || 0).toLocaleString('id-ID');

        // Level Progress
        if (this.myRank.current_level_xp && this.myRank.next_level_xp) {
            const progressPercent = (this.myRank.current_level_xp / this.myRank.next_level_xp) * 100;
            document.querySelector('#level-progress .h-full').style.width = progressPercent + '%';
            document.getElementById('level-text').textContent =
                `${this.myRank.current_level_xp} / ${this.myRank.next_level_xp} XP ke level ${this.myRank.current_level + 1}`;
        }
    }

    async loadGlobalLeaderboard(page = 1) {
        try {
            this.currentPage = page;
            const range = document.getElementById('filter-range').value || 'all';

            const res = await api.getGlobalLeaderboard(page, this.perPage, range);
            if (!res.success) throw new Error('Failed to load leaderboard');

            this.allLeaderboard = res.data?.data || [];
            this.filteredLeaderboard = [...this.allLeaderboard];

            this.renderLeaderboard();
            this.renderPagination(res.data?.pagination || {});
        } catch (error) {
            console.error('Leaderboard load error:', error);
            showToast('Gagal memuat leaderboard', 'error');
        }
    }

    renderLeaderboard() {
        const tbody = document.getElementById('leaderboard-body');

        if (this.filteredLeaderboard.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-600">
                        Tidak ada data leaderboard
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.filteredLeaderboard.map((entry, idx) => {
            const rankClass = idx === 0 ? 'bg-amber-50' : idx === 1 ? 'bg-slate-100' : idx === 2 ? 'bg-orange-50' : '';
            const medal = idx === 0 ? '🥇' : idx === 1 ? '🥈' : idx === 2 ? '🥉' : '';
            const isMe = this.myRank && entry.rank === this.myRank.rank;

            return `
                <tr class="${rankClass} border-b border-slate-200 hover:bg-teal-50/50 transition-colors ${isMe ? 'font-bold' : ''}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">${medal || entry.rank}</span>
                            ${isMe ? '<span class="text-xs bg-teal-500 text-white px-2 py-1 rounded">Anda</span>' : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-slate-900">${entry.user_name}</p>
                        <p class="text-xs text-slate-600">${entry.email}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <span class="material-symbols-rounded text-amber-500">star</span>
                            <span class="font-bold text-slate-900">${entry.current_level}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="font-bold text-teal-600">${entry.total_xp.toLocaleString('id-ID')}</p>
                        <p class="text-xs text-slate-600">XP</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="font-medium text-slate-900">${entry.completed_courses || 0}</p>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!pagination.last_page || pagination.last_page === 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        if (pagination.current_page > 1) {
            html += `<button onclick="leaderboard.loadGlobalLeaderboard(${pagination.current_page - 1})" class="px-3 py-1 rounded border border-slate-300 hover:bg-slate-100">← Sebelumnya</button>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<button class="px-3 py-1 bg-teal-500 text-white rounded">${i}</button>`;
            } else if (i <= 3 || i > pagination.last_page - 3 || Math.abs(i - pagination.current_page) <= 1) {
                html += `<button onclick="leaderboard.loadGlobalLeaderboard(${i})" class="px-3 py-1 rounded border border-slate-300 hover:bg-slate-100">${i}</button>`;
            } else if (i === 4 || i === pagination.last_page - 3) {
                html += `<span class="px-2">...</span>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="leaderboard.loadGlobalLeaderboard(${pagination.current_page + 1})" class="px-3 py-1 rounded border border-slate-300 hover:bg-slate-100">Berikutnya →</button>`;
        }

        container.innerHTML = html;
    }

    filterLeaderboard() {
        const search = document.getElementById('search-leaderboard').value.toLowerCase();
        this.filteredLeaderboard = this.allLeaderboard.filter(entry =>
            entry.user_name.toLowerCase().includes(search) ||
            entry.email.toLowerCase().includes(search)
        );
        this.renderLeaderboard();
    }

    async loadAchievements() {
        try {
            const res = await api.getMyAchievements();
            if (!res.success) throw new Error('Failed to load achievements');

            this.achievements = res.data || [];
            this.renderAchievements();
        } catch (error) {
            console.error('Achievements load error:', error);
            showToast('Gagal memuat achievements', 'error');
        }
    }

    renderAchievements() {
        const grid = document.getElementById('achievements-grid');

        if (this.achievements.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-symbols-rounded text-6xl text-slate-300">emoji_events</span>
                    <p class="text-slate-600 mt-4">Belum ada achievement yang disematkan</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = this.achievements.map(achievement => {
            const isUnlocked = achievement.is_unlocked;
            const unlockedDate = achievement.unlocked_at ?
                new Date(achievement.unlocked_at).toLocaleDateString('id-ID') : '-';

            return `
                <div class="bg-white border-2 ${isUnlocked ? 'border-amber-300 bg-amber-50' : 'border-slate-200'} rounded-lg p-6 text-center">
                    <div class="text-5xl mb-3 ${isUnlocked ? '' : 'opacity-30'}">${achievement.badge_icon || '🏆'}</div>
                    <h3 class="font-bold text-slate-900 mb-1">${achievement.name}</h3>
                    <p class="text-sm text-slate-600 mb-3">${achievement.description}</p>

                    ${isUnlocked ? `
                        <div class="inline-block px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                            ✓ Diperoleh: ${unlockedDate}
                        </div>
                    ` : `
                        <div class="inline-block px-3 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded">
                            Terkunci
                        </div>
                    `}
                </div>
            `;
        }).join('');
    }

    switchTab(tab) {
        this.currentTab = tab;

        // Update style
        document.getElementById('tab-global').classList.toggle('text-teal-600', tab === 'global');
        document.getElementById('tab-global').classList.toggle('border-teal-600', tab === 'global');
        document.getElementById('tab-global').classList.toggle('text-slate-600', tab !== 'global');
        document.getElementById('tab-global').classList.toggle('border-transparent', tab !== 'global');

        document.getElementById('tab-achievements').classList.toggle('text-teal-600', tab === 'achievements');
        document.getElementById('tab-achievements').classList.toggle('border-teal-600', tab === 'achievements');
        document.getElementById('tab-achievements').classList.toggle('text-slate-600', tab !== 'achievements');
        document.getElementById('tab-achievements').classList.toggle('border-transparent', tab !== 'achievements');

        // Toggle sections
        document.getElementById('global-section').classList.toggle('hidden', tab !== 'global');
        document.getElementById('achievements-section').classList.toggle('hidden', tab !== 'achievements');
    }
}

const leaderboard = new LeaderboardView();
leaderboard.load();
</script>
@endsection

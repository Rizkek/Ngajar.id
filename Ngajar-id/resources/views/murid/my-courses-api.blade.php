@extends('layouts.dashboard-api')

@section('title', 'Kursus Saya')
@section('header_title', 'Kursus Saya')

@section('content')
<div class="space-y-6">
    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-200">
        <button id="tab-active" onclick="myCourses.switchTab('active')"
            class="px-6 py-3 font-medium text-teal-600 border-b-2 border-teal-600 transition-colors">
            Sedang Belajar
        </button>
        <button id="tab-completed" onclick="myCourses.switchTab('completed')"
            class="px-6 py-3 font-medium text-slate-600 border-b-2 border-transparent hover:text-slate-900 transition-colors">
            Selesai
        </button>
    </div>

    <!-- Active Courses -->
    <div id="active-section" class="space-y-6">
        <!-- Filters -->
        <div class="flex gap-4 flex-wrap">
            <input type="text" id="search-courses" placeholder="Cari kursus..."
                @keyup.debounce="myCourses.filterCourses()"
                class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
            <select id="sort-courses" @change="myCourses.filterCourses()"
                class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="recent">Aktivitas Terbaru</option>
                <option value="name">Nama Kursus</option>
                <option value="progress">Progress</option>
            </select>
        </div>

        <!-- Courses Grid -->
        <div id="active-courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="text-center py-12 col-span-full">
                <div class="inline-flex items-center space-x-2">
                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce"></div>
                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Courses -->
    <div id="completed-section" class="hidden space-y-6">
        <!-- Filters -->
        <div class="flex gap-4 flex-wrap">
            <input type="text" id="search-completed" placeholder="Cari kursus selesai..."
                @keyup.debounce="myCourses.filterCourses()"
                class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        <!-- Completed Courses Grid -->
        <div id="completed-courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
class MyCourses {
    constructor() {
        this.activeCourses = [];
        this.completedCourses = [];
        this.currentTab = 'active';
    }

    async load() {
        try {
            const res = await api.getMyCourses();
            if (!res.success) throw new Error('Failed to load courses');

            const courses = res.data || [];

            // Separate active and completed
            this.activeCourses = courses.filter(c => !c.is_completed);
            this.completedCourses = courses.filter(c => c.is_completed);

            this.renderActiveCourses();
            this.renderCompletedCourses();
        } catch (error) {
            console.error('Load error:', error);
            showToast('Gagal memuat kursus', 'error');
        }
    }

    renderActiveCourses() {
        const grid = document.getElementById('active-courses-grid');

        if (this.activeCourses.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-symbols-rounded text-6xl text-slate-300">school</span>
                    <p class="text-slate-600 mt-4">Anda belum mendaftar kursus apapun</p>
                    <button onclick="window.location.href='/student/courses'"
                        class="mt-4 px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors">
                        Cari Kursus
                    </button>
                </div>
            `;
            return;
        }

        grid.innerHTML = this.activeCourses.map(course => this.createCourseCard(course)).join('');
    }

    renderCompletedCourses() {
        const grid = document.getElementById('completed-courses-grid');

        if (this.completedCourses.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-symbols-rounded text-6xl text-slate-300">done_all</span>
                    <p class="text-slate-600 mt-4">Belum ada kursus yang diselesaikan</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = this.completedCourses.map(course => this.createCompletedCard(course)).join('');
    }

    createCourseCard(course) {
        const progressPercent = course.progress_percentage || 0;
        const lastAccessed = course.last_accessed_at ?
            new Date(course.last_accessed_at).toLocaleDateString('id-ID') : 'Belum dimulai';

        return `
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                <div class="aspect-video bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white relative">
                    <span class="material-symbols-rounded text-5xl">menu_book</span>
                    <div class="absolute top-3 right-3 bg-white text-slate-900 text-xs font-bold px-2 py-1 rounded-full">
                        ${progressPercent}%
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-slate-900 line-clamp-2 mb-2">${course.judul}</h3>
                    <p class="text-xs text-slate-600 mb-3">Instruktur: ${course.instructor_name}</p>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-medium text-slate-700">Progress</p>
                            <p class="text-xs text-slate-600">${progressPercent}%</p>
                        </div>
                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-teal-400 to-teal-600 transition-all duration-300"
                                style="width: ${progressPercent}%"></div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                        <div class="flex items-center gap-1 text-slate-600">
                            <span class="material-symbols-rounded text-sm">description</span>
                            <span>${course.material_count || 0} materi</span>
                        </div>
                        <div class="flex items-center gap-1 text-slate-600">
                            <span class="material-symbols-rounded text-sm">access_time</span>
                            <span>${lastAccessed}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button onclick="window.location.href='/student/course-progress?id=${course.kelas_id}'"
                            class="flex-1 px-3 py-2 bg-teal-500 text-white text-sm rounded-lg hover:bg-teal-600 transition-colors">
                            Lanjutkan
                        </button>
                        <button onclick="window.location.href='/student/course/${course.kelas_id}'"
                            class="flex-1 px-3 py-2 border border-slate-300 text-stone-slate-600 text-sm rounded-lg hover:bg-slate-50 transition-colors">
                            Detail
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    createCompletedCard(course) {
        const completedDate = course.completed_at ?
            new Date(course.completed_at).toLocaleDateString('id-ID') : '-';

        return `
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden relative">
                <!-- Completed Badge -->
                <div class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 z-10">
                    <span class="material-symbols-rounded text-sm">check</span>
                    Selesai
                </div>

                <div class="aspect-video bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center text-white">
                    <span class="material-symbols-rounded text-5xl">school</span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-slate-900 line-clamp-2 mb-2">${course.judul}</h3>
                    <p class="text-xs text-slate-600 mb-2">Instruktur: ${course.instructor_name}</p>

                    <!-- Grade if available -->
                    ${course.final_grade ? `
                        <div class="mb-3 p-2 bg-green-50 rounded text-center">
                            <p class="text-xs text-slate-600">Nilai Akhir</p>
                            <p class="text-lg font-bold text-green-600">${course.final_grade}</p>
                        </div>
                    ` : ''}

                    <!-- Completion Info -->
                    <div class="text-xs text-slate-600 mb-4 space-y-1">
                        <p>✓ Selesai pada: <strong>${completedDate}</strong></p>
                        ${course.certificate_id ? '<p>✓ Sertifikat tersedia</p>' : ''}
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        ${course.certificate_id ? `
                            <button onclick="window.open('/student/certificate/${course.certificate_id}', '_blank')"
                                class="flex-1 px-3 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 transition-colors flex items-center justify-center gap-1">
                                <span class="material-symbols-rounded text-sm">card_membership</span>
                                Sertifikat
                            </button>
                        ` : ''}
                        <button onclick="window.location.href='/student/course/${course.kelas_id}'"
                            class="flex-1 px-3 py-2 border border-slate-300 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition-colors">
                            Lihat
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    switchTab(tab) {
        this.currentTab = tab;

        // Update active tab styling
        document.getElementById('tab-active').classList.toggle('text-teal-600', tab === 'active');
        document.getElementById('tab-active').classList.toggle('border-teal-600', tab === 'active');
        document.getElementById('tab-active').classList.toggle('text-slate-600', tab !== 'active');
        document.getElementById('tab-active').classList.toggle('border-transparent', tab !== 'active');

        document.getElementById('tab-completed').classList.toggle('text-teal-600', tab === 'completed');
        document.getElementById('tab-completed').classList.toggle('border-teal-600', tab === 'completed');
        document.getElementById('tab-completed').classList.toggle('text-slate-600', tab !== 'completed');
        document.getElementById('tab-completed').classList.toggle('border-transparent', tab !== 'completed');

        // Toggle sections
        document.getElementById('active-section').classList.toggle('hidden', tab !== 'active');
        document.getElementById('completed-section').classList.toggle('hidden', tab !== 'completed');
    }

    filterCourses() {
        if (this.currentTab === 'active') {
            const search = document.getElementById('search-courses').value.toLowerCase();
            const sort = document.getElementById('sort-courses').value;

            let filtered = this.activeCourses.filter(c =>
                c.judul.toLowerCase().includes(search) ||
                c.instructor_name.toLowerCase().includes(search)
            );

            // Sort
            if (sort === 'name') {
                filtered.sort((a, b) => a.judul.localeCompare(b.judul));
            } else if (sort === 'progress') {
                filtered.sort((a, b) => (b.progress_percentage || 0) - (a.progress_percentage || 0));
            }

            const grid = document.getElementById('active-courses-grid');
            if (filtered.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-12 text-slate-600">Kursus tidak ditemukan</div>`;
            } else {
                grid.innerHTML = filtered.map(c => this.createCourseCard(c)).join('');
            }
        } else {
            const search = document.getElementById('search-completed').value.toLowerCase();
            let filtered = this.completedCourses.filter(c =>
                c.judul.toLowerCase().includes(search)
            );

            const grid = document.getElementById('completed-courses-grid');
            if (filtered.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-12 text-slate-600">Kursus tidak ditemukan</div>`;
            } else {
                grid.innerHTML = filtered.map(c => this.createCompletedCard(c)).join('');
            }
        }
    }
}

const myCourses = new MyCourses();
myCourses.load();
</script>
@endsection

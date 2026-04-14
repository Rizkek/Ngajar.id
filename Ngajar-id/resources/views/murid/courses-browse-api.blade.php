@extends('layouts.dashboard-api')

@section('title', 'Cari Kursus')
@section('header_title', 'Cari Kursus')

@section('content')
<div class="space-y-6">
    <!-- Search & Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Cari Kursus</label>
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Cari kursus..."
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                        @keyup.debounce="loadCourses()">
                    <span class="material-symbols-rounded absolute right-3 top-2.5 text-slate-400">search</span>
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                <select id="category-filter" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                    @change="loadCourses()">
                    <option value="">Semua Kategori</option>
                </select>
            </div>

            <!-- Level Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Level</label>
                <select id="level-filter" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                    @change="loadCourses()">
                    <option value="">Semua Level</option>
                    <option value="beginner">Pemula</option>
                    <option value="intermediate">Menengah</option>
                    <option value="advanced">Lanjutan</option>
                </select>
            </div>
        </div>

        <!-- Sort & Additional Options -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Urutkan</label>
                <select id="sort-filter" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                    @change="loadCourses()">
                    <option value="newest">Terbaru</option>
                    <option value="popular">Paling Populer</option>
                    <option value="rating">Rating Tertinggi</option>
                    <option value="price_low">Harga Termurah</option>
                    <option value="price_high">Harga Termahal</option>
                </select>
            </div>

            <!-- Results Count -->
            <div class="flex items-end">
                <button @click="clearFilters()" class="px-4 py-2 text-sm font-medium text-teal-600 hover:bg-teal-50 rounded-lg transition-colors">
                    ↻ Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Filter (Mobile Hidden) -->
        <div class="hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sticky top-24">
                <h3 class="font-bold text-slate-900 mb-4">Filter Tambahan</h3>

                <!-- Price Range -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Range Harga</label>
                    <div class="space-y-2 text-sm" id="price-ranges">
                        <label class="flex items-center">
                            <input type="radio" name="price" value="" class="mr-2" @change="loadCourses()" checked>
                            <span>Semua Harga</span>
                        </label>
                    </div>
                </div>

                <!-- Instructor Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Instruktur</label>
                    <input type="text" id="instructor-search" placeholder="Cari instruktur..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                        @keyup.debounce="loadCourses()">
                </div>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Hasil Pencarian</h2>
                <p id="result-count" class="text-sm text-slate-600">Memuat...</p>
            </div>

            <div id="courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Loading skeleton -->
                <div class="text-center py-12 col-span-full">
                    <div class="inline-flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce"></div>
                        <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="flex items-center justify-center gap-2 mt-8">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
class CoursesBrowser {
    constructor() {
        this.currentPage = 1;
        this.perPage = 12;
        this.filters = {
            q: '',
            category: '',
            level: '',
            sort_by: 'newest',
            min_price: 0,
            max_price: 99999999,
        };
    }

    async loadFilters() {
        try {
            const filters = await api.getBrowseFilters();
            if (filters.success && filters.data) {
                // Load categories
                const categories = document.getElementById('category-filter');
                filters.data.categories?.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat;
                    option.textContent = cat;
                    categories.appendChild(option);
                });

                // Load price ranges
                const priceRanges = document.getElementById('price-ranges');
                priceRanges.innerHTML = '<label class="flex items-center"><input type="radio" name="price" value="" class="mr-2" @change="loadCourses()" checked><span>Semua Harga</span></label>';
                filters.data.price_ranges?.forEach(range => {
                    const label = document.createElement('label');
                    label.className = 'flex items-center';
                    label.innerHTML = `
                        <input type="radio" name="price" value="${range.min},${range.max}" class="mr-2" @change="browser.onPriceChange()">
                        <span>${range.label}</span>
                    `;
                    priceRanges.appendChild(label);
                });
            }
        } catch (error) {
            console.error('Filter load error:', error);
        }
    }

    async loadCourses(page = 1) {
        try {
            this.currentPage = page;

            // Update filters from UI
            this.filters.q = document.getElementById('search-input').value;
            this.filters.category = document.getElementById('category-filter').value;
            this.filters.level = document.getElementById('level-filter').value;
            this.filters.sort_by = document.getElementById('sort-filter').value;
            this.filters.page = page;
            this.filters.per_page = this.perPage;

            const coursesRes = await api.searchCourses(this.filters.q, this.filters);

            if (!coursesRes.success) throw new Error('Failed to load courses');

            const courses = coursesRes.data?.data || [];
            const pagination = coursesRes.data?.pagination || {};

            // Update result count
            document.getElementById('result-count').textContent =
                `${pagination.total || 0} kursus ditemukan`;

            // Render courses
            const grid = document.getElementById('courses-grid');
            if (courses.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <span class="material-symbols-rounded text-6xl text-slate-300">search_off</span>
                        <p class="text-slate-600 mt-4">Tidak ada kursus yang ditemukan</p>
                    </div>
                `;
            } else {
                grid.innerHTML = courses.map(course => `
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-video bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white relative overflow-hidden">
                            <span class="material-symbols-rounded text-5xl">menu_book</span>
                            ${course.average_rating ? `<div class="absolute top-2 right-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full">⭐ ${course.average_rating}</div>` : ''}
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 line-clamp-2 mb-1">${course.judul}</h3>
                            <p class="text-xs text-slate-600 mb-2">oleh ${course.instructor_name}</p>
                            <p class="text-xs text-slate-500 mb-3">${course.level}</p>

                            ${course.review_count ? `<p class="text-xs text-slate-600 mb-3">${course.review_count} ulasan</p>` : ''}

                            <div class="flex items-center justify-between">
                                <p class="font-bold text-teal-600">Rp ${course.harga?.toLocaleString('id-ID') || '0'}</p>
                                <button onclick="window.location.href='/student/course/${course.kelas_id}'"
                                    class="px-3 py-1 bg-teal-500 text-white text-sm rounded-lg hover:bg-teal-600 transition-colors">
                                    Lihat
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // Render pagination
            this.renderPagination(pagination);

        } catch (error) {
            console.error('Course load error:', error);
            showToast('Gagal memuat kursus', 'error');
        }
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
            html += `<button onclick="browser.loadCourses(${pagination.current_page - 1})" class="px-3 py-1 rounded border border-slate-300 hover:bg-slate-100">← Sebelumnya</button>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<button class="px-3 py-1 bg-teal-500 text-white rounded">${i}</button>`;
            } else if (i <= 3 || i > pagination.last_page - 3 || Math.abs(i - pagination.current_page) <= 1) {
                html += `<button onclick="browser.loadCourses(${i})" class="px-3 py-1 rounded border border-slate-300 hover:bg-slate-100">${i}</button>`;
            } else if (i === 4 || i === pagination.last_page - 3) {
                html += `<span class="px-2">...</span>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="browser.loadCourses(${pagination.current_page + 1})" class="px-3 py-1 rounded border border-slate-300 hover:bg-slate-100">Berikutnya →</button>`;
        }

        container.innerHTML = html;
    }

    clearFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('category-filter').value = '';
        document.getElementById('level-filter').value = '';
        document.getElementById('sort-filter').value = 'newest';
        document.querySelector('input[name="price"][value=""]').checked = true;
        document.getElementById('instructor-search').value = '';
        this.loadCourses(1);
    }

    onPriceChange() {
        this.loadCourses(1);
    }
}

// Initialize browser
const browser = new CoursesBrowser();
browser.loadFilters();
browser.loadCourses(1);
</script>
@endsection

@extends('layouts.dashboard-api')

@section('title', 'Detail Kursus')
@section('header_title', 'Detail Kursus')

@section('content')
<div id="course-container" class="space-y-6">
    <!-- Loading State -->
    <div class="text-center py-12">
        <div class="inline-flex items-center space-x-2">
            <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce"></div>
            <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.1s"></div>
            <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.2s"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
class CourseDetailView {
    constructor() {
        this.courseId = new URLSearchParams(window.location.search).get('id') ||
                       window.location.pathname.split('/').pop();
        this.course = null;
        this.eligibility = null;
        this.isEnrolled = false;
    }

    async loadCourseDetail() {
        try {
            // Get course detail
            const courseRes = await api.getCourseDetail(this.courseId);
            if (!courseRes.success) throw new Error('Gagal memuat detail kursus');

            this.course = courseRes.data;

            // Check eligibility & enrollment status
            const eligRes = await api.checkEnrollmentEligibility(this.courseId);
            this.eligibility = eligRes.data || {};
            this.isEnrolled = this.eligibility.is_enrolled || false;

            // Get reviews
            const reviewsRes = await api.getCourseReviews(this.courseId);
            const reviews = reviewsRes.data || [];

            this.renderCourse(reviews);
        } catch (error) {
            console.error('Course load error:', error);
            document.getElementById('course-container').innerHTML = `
                <div class="text-center py-12">
                    <span class="material-symbols-rounded text-6xl text-slate-300">error</span>
                    <p class="text-slate-600 mt-4">${error.message}</p>
                </div>
            `;
        }
    }

    renderCourse(reviews) {
        const course = this.course;
        const html = `
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-sky-500 to-blue-600 rounded-xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8 text-white">
                    <!-- Info -->
                    <div class="lg:col-span-2">
                        <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium mb-4">
                            ${course.level}
                        </div>
                        <h1 class="text-4xl font-bold mb-4">${course.judul}</h1>
                        <p class="text-lg opacity-90 mb-6">${course.deskripsi}</p>
                        <div class="flex items-center gap-4 flex-wrap">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded">person</span>
                                <span>${course.instructor_name}</span>
                            </div>
                            ${course.average_rating ? `
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded">star</span>
                                    <span>${course.average_rating} (${course.review_count} ulasan)</span>
                                </div>
                            ` : ''}
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded">people</span>
                                <span>${course.enrolled_count || 0} peserta</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6 text-slate-900">
                        <div class="text-3xl font-bold text-teal-600 mb-4">
                            Rp ${course.harga?.toLocaleString('id-ID') || '0'}
                        </div>

                        ${this.renderEnrollmentButton()}

                        <div class="mt-6 space-y-3 text-sm">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-teal-600 flex-shrink-0">check_circle</span>
                                <span>Akses selamanya</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-teal-600 flex-shrink-0">check_circle</span>
                                <span>${course.material_count || 0} materi pembelajaran</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-teal-600 flex-shrink-0">check_circle</span>
                                <span>Sertifikat resmi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eligibility Warnings -->
            ${this.renderEligibilityWarnings()}

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tentang Kursus -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">Tentang Kursus</h2>
                        <p class="text-slate-700 leading-relaxed">${course.deskripsi}</p>
                    </div>

                    <!-- Apa yang Akan Dipelajari -->
                    ${course.learning_objectives ? `
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h2 class="text-2xl font-bold text-slate-900 mb-4">Apa yang Akan Dipelajari</h2>
                            <ul class="space-y-2">
                                ${course.learning_objectives.split('\\n').map(obj => `
                                    <li class="flex items-start gap-3">
                                        <span class="material-symbols-rounded text-teal-600 flex-shrink-0">check</span>
                                        <span class="text-slate-700">${obj}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    ` : ''}

                    <!-- Materi Kursus -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">Materi Kursus</h2>
                        <div class="space-y-2" id="materials-list">
                            <p class="text-slate-600">Memuat materi...</p>
                        </div>
                    </div>

                    <!-- Ulasan -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">Ulasan Peserta</h2>

                        ${this.isEnrolled ? `
                            <div class="mb-6 pb-6 border-b border-slate-200">
                                <p class="text-sm text-slate-600 mb-3">Bagikan pengalaman kamu belajar di kursus ini</p>
                                <button onclick="courseDetail.showReviewForm()"
                                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors">
                                    Tulis Ulasan
                                </button>
                            </div>
                        ` : ''}

                        <div id="reviews-list" class="space-y-4">
                            ${reviews.length === 0 ? '<p class="text-slate-600">Belum ada ulasan</p>' : ''}
                            ${reviews.map(review => `
                                <div class="border-b border-slate-200 pb-4 last:border-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-medium text-slate-900">${review.user_name}</p>
                                        <p class="text-amber-500">{'⭐'.repeat(review.rating)}</p>
                                    </div>
                                    <p class="text-slate-700 text-sm mb-1">${review.review_text}</p>
                                    <p class="text-xs text-slate-500">${new Date(review.created_at).toLocaleDateString('id-ID')}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Persyaratan -->
                    ${this.eligibility.requirements && Object.keys(this.eligibility.requirements).length > 0 ? `
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="font-bold text-slate-900 mb-4">Persyaratan</h3>
                            <ul class="space-y-2 text-sm">
                                ${Object.entries(this.eligibility.requirements).map(([key, value]) => `
                                    <li class="flex items-start gap-2">
                                        <span class="material-symbols-rounded text-sm ${value ? 'text-green-600' : 'text-red-600'}">
                                            ${value ? 'check_circle' : 'cancel'}
                                        </span>
                                        <span class="text-slate-700">${this.formatRequirement(key)}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    ` : ''}

                    <!-- Instruktur -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Instruktur</h3>
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white text-2xl mx-auto mb-3">
                                👨‍🏫
                            </div>
                            <p class="font-medium text-slate-900">${course.instructor_name}</p>
                            <p class="text-sm text-slate-600 mt-2">${course.instructor_bio || 'Instruktur berpengalaman'}</p>
                        </div>
                    </div>

                    <!-- Info Kursus -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Informasi Kursus</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-slate-600">Kategori</p>
                                <p class="font-medium text-slate-900">${course.category_name || 'Umum'}</p>
                            </div>
                            <div>
                                <p class="text-slate-600">Level</p>
                                <p class="font-medium text-slate-900">${course.level}</p>
                            </div>
                            <div>
                                <p class="text-slate-600">Durasi Kursus</p>
                                <p class="font-medium text-slate-900">${course.duration_weeks || '4'} minggu</p>
                            </div>
                            <div>
                                <p class="text-slate-600">Jumlah Peserta</p>
                                <p class="font-medium text-slate-900">${course.enrolled_count || 0}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form Modal (Hidden) -->
            <div id="review-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Tulis Ulasan</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Rating</label>
                        <div class="flex gap-2">
                            ${[1,2,3,4,5].map(i => `
                                <button onclick="courseDetail.setRating(${i})"
                                    class="text-2xl transition-transform hover:scale-110 rating-btn"
                                    data-rating="${i}">⭐</button>
                            `).join('')}
                        </div>
                        <input type="hidden" id="review-rating" value="5">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Ulasan</label>
                        <textarea id="review-text" placeholder="Bagikan pengalaman kamu..."
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            rows="4"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button @click="document.getElementById('review-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button onclick="courseDetail.submitReview()"
                            class="flex-1 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors">
                            Kirim
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('course-container').innerHTML = html;

        // Load materials
        this.loadMaterials();
    }

    async loadMaterials() {
        try {
            const res = await api.getCourseDetail(this.courseId);
            const materials = res.data?.materials || [];

            const list = document.getElementById('materials-list');
            if (materials.length === 0) {
                list.innerHTML = '<p class="text-slate-600">Tidak ada materi</p>';
                return;
            }

            list.innerHTML = materials.map(mat => `
                <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                    <span class="material-symbols-rounded text-teal-600 flex-shrink-0">description</span>
                    <div class="flex-1">
                        <p class="font-medium text-slate-900">${mat.judul}</p>
                        <p class="text-sm text-slate-600">${mat.jenis || 'Materi'}</p>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Materials load error:', error);
        }
    }

    renderEligibilityWarnings() {
        if (!this.eligibility || this.eligibility.is_eligible) return '';

        let warnings = [];
        if (this.eligibility.reason === 'level_requirement') {
            warnings.push('Anda belum mencapai level yang diperlukan untuk mengikuti kursus ini');
        } else if (this.eligibility.reason === 'prerequisite_not_met') {
            warnings.push(`Anda harus menyelesaikan kursus ${this.eligibility.required_course} terlebih dahulu`);
        }

        return warnings.map(msg => `
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <span class="material-symbols-rounded text-amber-600">warning</span>
                    <p class="text-amber-800">${msg}</p>
                </div>
            </div>
        `).join('');
    }

    renderEnrollmentButton() {
        if (this.isEnrolled) {
            return `
                <button onclick="window.location.href='/student/my-courses'"
                    class="w-full px-4 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded">check</span>
                    Sudah Terdaftar
                </button>
                <button onclick="window.location.href='/student/course-progress?id=${this.courseId}'"
                    class="w-full mt-2 px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    Lanjutkan Belajar
                </button>
            `;
        }

        const canEnroll = this.eligibility?.is_eligible;
        return `
            <button onclick="courseDetail.enrollCourse()"
                class="w-full px-4 py-3 ${canEnroll ? 'bg-teal-500 hover:bg-teal-600' : 'bg-slate-300 cursor-not-allowed'} text-white rounded-lg transition-colors font-medium flex items-center justify-center gap-2"
                ${canEnroll ? '' : 'disabled'}>
                <span class="material-symbols-rounded">add</span>
                ${canEnroll ? 'Daftar Sekarang' : 'Tidak Bisa Daftar'}
            </button>
        `;
    }

    async enrollCourse() {
        if (!this.eligibility?.is_eligible) {
            showToast('Anda tidak memenuhi syarat untuk mengikuti kursus ini', 'error');
            return;
        }

        try {
            const res = await api.enrollCourse(this.courseId);
            if (res.success) {
                showToast('Berhasil mendaftar kursus!', 'success');
                this.isEnrolled = true;
                setTimeout(() => this.loadCourseDetail(), 1500);
            } else {
                showToast(res.message || 'Gagal mendaftar kursus', 'error');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    showReviewForm() {
        document.getElementById('review-modal').classList.remove('hidden');
    }

    setRating(rating) {
        document.getElementById('review-rating').value = rating;
        document.querySelectorAll('.rating-btn').forEach((btn, idx) => {
            if (idx < rating) {
                btn.style.opacity = '1';
            } else {
                btn.style.opacity = '0.3';
            }
        });
    }

    async submitReview() {
        try {
            const rating = parseInt(document.getElementById('review-rating').value);
            const text = document.getElementById('review-text').value;

            if (!text.trim()) {
                showToast('Tulis ulasan terlebih dahulu', 'error');
                return;
            }

            const res = await api.addCourseReview(this.courseId, rating, text);
            if (res.success) {
                showToast('Ulasan berhasil dikirim!', 'success');
                document.getElementById('review-modal').classList.add('hidden');
                this.loadCourseDetail();
            } else {
                showToast(res.message || 'Gagal mengirim ulasan', 'error');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    formatRequirement(key) {
        const map = {
            'min_level': 'Level Minimum',
            'max_level': 'Level Maksimum',
            'prerequisite': 'Kursus Prasyarat',
            'learning_path': 'Learning Path Requirement',
        };
        return map[key] || key;
    }
}

const courseDetail = new CourseDetailView();
courseDetail.loadCourseDetail();
</script>
@endsection

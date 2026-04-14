@extends('layouts.dashboard-api')

@section('title', 'Sertifikat Saya')
@section('header_title', 'Sertifikat Saya')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-blue-100 text-sm">Total Sertifikat</p>
                    <p id="total-cert" class="text-4xl font-bold">-</p>
                </div>
                <span class="material-symbols-rounded text-4xl">list_alt</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-green-100 text-sm">Bulan Ini</p>
                    <p id="month-cert" class="text-4xl font-bold">-</p>
                </div>
                <span class="material-symbols-rounded text-4xl">event_available</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-purple-100 text-sm">Rata-rata Nilai</p>
                    <p id="avg-grade" class="text-4xl font-bold">-</p>
                </div>
                <span class="material-symbols-rounded text-4xl">grade</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex gap-4 flex-wrap">
        <input type="text" id="search-cert" placeholder="Cari sertifikat..."
            @keyup.debounce="certificates.filterCertificates()"
            class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 flex-1 max-w-sm">
        <select id="sort-cert" @change="certificates.filterCertificates()"
            class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
            <option value="newest">Terbaru</option>
            <option value="oldest">Terlama</option>
            <option value="grade">Nilai Tertinggi</option>
        </select>
    </div>

    <!-- Certificates Grid -->
    <div id="certificates-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading -->
        <div class="text-center py-12 col-span-full">
            <div class="inline-flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce"></div>
                <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 rounded-full bg-teal-400 animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
</div>

<!-- Certificate Preview Modal -->
<div id="cert-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-screen overflow-auto">
        <div class="sticky top-0 bg-white border-b border-slate-200 p-6 flex items-center justify-between">
            <h2 id="modal-title" class="text-2xl font-bold text-slate-900">Preview Sertifikat</h2>
            <button onclick="certificates.closeModal()" class="text-slate-600 hover:text-slate-900">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- Certificate Preview -->
            <div id="cert-preview" class="bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-300 rounded-lg p-8 text-center">
                <!-- Will be filled by JS -->
            </div>

            <!-- Certificate Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Nomor Sertifikat</p>
                    <p id="cert-number" class="font-bold text-slate-900">-</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Tanggal Diperoleh</p>
                    <p id="cert-date" class="font-bold text-slate-900">-</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Nilai Akhir</p>
                    <p id="cert-grade" class="font-bold text-slate-900">-</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm text-slate-600 mb-1">Status</p>
                    <p id="cert-status" class="font-bold text-green-600">Terverifikasi</p>
                </div>
            </div>

            <!-- Verification Info -->
            <div class="border border-slate-200 rounded-lg p-4">
                <h3 class="font-bold text-slate-900 mb-3">Verifikasi Sertifikat</h3>
                <div class="space-y-2 text-sm">
                    <p class="text-slate-600">Verifikasi keaslian sertifikat ini melalui link di bawah:</p>
                    <div class="bg-slate-50 p-3 rounded border border-slate-200 break-all">
                        <p id="verify-link" class="text-xs text-slate-600">-</p>
                    </div>
                    <button id="copy-link-btn" onclick="certificates.copyVerifyLink()"
                        class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                        📋 Salin Link
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2 border-t border-slate-200 pt-4">
                <button id="verify-btn" onclick="certificates.verifyCertificate()"
                    class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded">check_circle</span>
                    Verifikasi
                </button>
                <button id="download-btn" onclick="certificates.downloadCertificate()"
                    class="flex-1 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded">download</span>
                    Download PDF
                </button>
                <button id="share-btn" onclick="certificates.shareCertificate()"
                    class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded">share</span>
                    Bagikan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
class CertificatesView {
    constructor() {
        this.allCertificates = [];
        this.filteredCertificates = [];
        this.currentCertificate = null;
    }

    async load() {
        try {
            const res = await api.getMyCertificates();
            if (!res.success) throw new Error('Failed to load certificates');

            this.allCertificates = res.data || [];
            this.filteredCertificates = [...this.allCertificates];

            this.updateStats();
            this.renderCertificates();
        } catch (error) {
            console.error('Load error:', error);
            showToast('Gagal memuat sertifikat', 'error');
        }
    }

    updateStats() {
        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();

        const monthCerts = this.allCertificates.filter(cert => {
            const certDate = new Date(cert.completed_at);
            return certDate.getMonth() === currentMonth && certDate.getFullYear() === currentYear;
        });

        const grades = this.allCertificates.map(c => parseFloat(c.final_grade) || 0).filter(g => g > 0);
        const avgGrade = grades.length > 0 ? (grades.reduce((a, b) => a + b) / grades.length).toFixed(1) : 0;

        document.getElementById('total-cert').textContent = this.allCertificates.length;
        document.getElementById('month-cert').textContent = monthCerts.length;
        document.getElementById('avg-grade').textContent = avgGrade;
    }

    renderCertificates() {
        const grid = document.getElementById('certificates-grid');

        if (this.filteredCertificates.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-symbols-rounded text-6xl text-slate-300">card_membership</span>
                    <p class="text-slate-600 mt-4">Belum ada sertifikat</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = this.filteredCertificates.map(cert => {
            const completedDate = new Date(cert.completed_at).toLocaleDateString('id-ID');
            const gradeColor = cert.final_grade >= 85 ? 'text-green-600' : cert.final_grade >= 70 ? 'text-blue-600' : 'text-orange-600';

            return `
                <div class="bg-white border-2 border-amber-300 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Certificate Preview Thumbnail -->
                    <div class="bg-gradient-to-br from-amber-100 to-yellow-100 h-48 flex items-center justify-center relative overflow-hidden">
                        <div class="text-center">
                            <span class="material-symbols-rounded text-6xl text-amber-600 opacity-30">card_membership</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="font-bold text-slate-900 line-clamp-2 mb-2">${cert.course_name}</h3>
                        <p class="text-xs text-slate-600 mb-3">${cert.instructor_name}</p>

                        <!-- Grade -->
                        <div class="mb-4 p-3 bg-slate-50 rounded-lg text-center">
                            <p class="text-xs text-slate-600 mb-1">Nilai Akhir</p>
                            <p class="text-2xl font-bold ${gradeColor}">${cert.final_grade}</p>
                        </div>

                        <!-- Date -->
                        <p class="text-xs text-slate-600 mb-3">Diperoleh: ${completedDate}</p>

                        <!-- Cert Number (truncated) -->
                        <p class="text-xs text-slate-500 mb-4 font-mono bg-slate-50 p-2 rounded truncate">
                            #${cert.certificate_number}
                        </p>

                        <!-- Actions -->
                        <div class="space-y-2">
                            <button onclick="certificates.viewCertificate('${cert.certificate_id}')"
                                class="w-full px-3 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 transition-colors">
                                Lihat
                            </button>
                            <button onclick="certificates.shareDirectly('${cert.certificate_id}')"
                                class="w-full px-3 py-2 border border-slate-300 text-slate-700 text-sm rounded-lg hover:bg-slate-50 transition-colors flex items-center justify-center gap-1">
                                <span class="material-symbols-rounded text-sm">share</span>
                                Bagikan
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    filterCertificates() {
        const search = document.getElementById('search-cert').value.toLowerCase();
        const sort = document.getElementById('sort-cert').value;

        this.filteredCertificates = this.allCertificates.filter(cert =>
            cert.course_name.toLowerCase().includes(search) ||
            cert.instructor_name.toLowerCase().includes(search)
        );

        // Sort
        if (sort === 'oldest') {
            this.filteredCertificates.sort((a, b) => new Date(a.completed_at) - new Date(b.completed_at));
        } else if (sort === 'grade') {
            this.filteredCertificates.sort((a, b) => parseFloat(b.final_grade) - parseFloat(a.final_grade));
        } else {
            this.filteredCertificates.sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at));
        }

        this.renderCertificates();
    }

    async viewCertificate(certId) {
        try {
            this.currentCertificate = this.allCertificates.find(c => c.certificate_id === certId);
            if (!this.currentCertificate) throw new Error('Certificate not found');

            // Render modal
            document.getElementById('modal-title').textContent = `Sertifikat - ${this.currentCertificate.course_name}`;

            const preview = `
                <div class="space-y-6">
                    <div class="text-center">
                        <p class="text-amber-700 text-sm font-medium tracking-widest mb-2">------- NGAJAR.ID CERTIFICATE -------</p>
                        <h1 class="text-3xl font-bold text-amber-900 mb-4">SERTIFIKAT KELULUSAN</h1>
                    </div>

                    <div class="text-center space-y-4 py-6">
                        <p class="text-sm text-amber-700">Dengan ini diberikan kepada</p>
                        <p class="text-2xl font-bold text-amber-900">${this.currentCertificate.user_name.toUpperCase()}</p>

                        <hr class="border-amber-300 my-6">

                        <p class="text-sm text-amber-700 mb-2">Telah menyelesaikan dengan sukses kursus</p>
                        <p class="text-xl font-bold text-amber-900">${this.currentCertificate.course_name}</p>

                        <p class="text-sm text-amber-700 mt-6">dengan nilai akhir</p>
                        <p class="text-3xl font-bold text-green-600">${this.currentCertificate.final_grade}</p>

                        <p class="text-sm text-amber-700 mt-6">Tanggal: ${new Date(this.currentCertificate.completed_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    </div>
                </div>
            `;

            document.getElementById('cert-preview').innerHTML = preview;
            document.getElementById('cert-number').textContent = `#${this.currentCertificate.certificate_number}`;
            document.getElementById('cert-date').textContent = new Date(this.currentCertificate.completed_at).toLocaleDateString('id-ID');
            document.getElementById('cert-grade').textContent = this.currentCertificate.final_grade;
            document.getElementById('verify-link').textContent = `${window.location.origin}/verify/${this.currentCertificate.certificate_id}`;

            document.getElementById('cert-modal').classList.remove('hidden');
        } catch (error) {
            console.error('View certificate error:', error);
            showToast('Gagal membuka sertifikat', 'error');
        }
    }

    closeModal() {
        document.getElementById('cert-modal').classList.add('hidden');
        this.currentCertificate = null;
    }

    copyVerifyLink() {
        const link = document.getElementById('verify-link').textContent;
        navigator.clipboard.writeText(link).then(() => {
            showToast('Link disalin ke clipboard', 'success');
        });
    }

    async verifyCertificate() {
        if (!this.currentCertificate) return;
        try {
            const res = await api.verifyCertificate(this.currentCertificate.certificate_id);
            if (res.data?.is_valid) {
                showToast('Sertifikat terverifikasi ✓', 'success');
            } else {
                showToast('Sertifikat tidak valid', 'error');
            }
        } catch (error) {
            showToast('Gagal memverifikasi', 'error');
        }
    }

    async downloadCertificate() {
        if (!this.currentCertificate) return;
        try {
            // Download endpoint not yet implemented; using window.open as fallback
            window.open(`/student/certificate/download/${this.currentCertificate.certificate_id}`, '_blank');
            showToast('Download dimulai...', 'success');
        } catch (error) {
            showToast('Gagal mendownload', 'error');
        }
    }

    async shareCertificate() {
        if (!this.currentCertificate) return;
        const verifyLink = `${window.location.origin}/verify/${this.currentCertificate.certificate_id}`;
        const shareText = `Saya telah menyelesaikan kursus "${this.currentCertificate.course_name}" di Ngajar.id! Cek sertifikat saya: ${verifyLink}`;

        if (navigator.share) {
            try {
                await navigator.share({ title: 'Sertifikat Ngajar.id', text: shareText });
            } catch (err) {
                console.log('Share cancelled');
            }
        } else {
            navigator.clipboard.writeText(shareText);
            showToast('Teks dibagikan ke clipboard', 'success');
        }
    }

    async shareDirectly(certId) {
        const cert = this.allCertificates.find(c => c.certificate_id === certId);
        if (!cert) return;
        const verifyLink = `${window.location.origin}/verify/${certId}`;
        await this.viewCertificate(certId);
        this.shareCertificate();
    }
}

const certificates = new CertificatesView();
certificates.load();
</script>
@endsection

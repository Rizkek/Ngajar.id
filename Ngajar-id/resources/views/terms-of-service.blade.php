@extends('layouts.app')

@section('title', 'Syarat & Ketentuan - Ngajar.ID')

@section('content')
    <section class="bg-gradient-to-b from-orange-50 to-white pt-24 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Syarat & <span
                        class="text-orange-600">Ketentuan</span></h1>
                <p class="text-slate-500">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl shadow-orange-100/50 border border-orange-50"
                data-aos="fade-up">
                <div class="prose prose-slate max-w-none space-y-8">
                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-base">1</span>
                            Ketentuan Pengguna
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Dengan mengakses dan menggunakan Ngajar.id, Anda setuju untuk terikat oleh Syarat dan Ketentuan
                            ini. Jika Anda tidak setuju, mohon untuk tidak menggunakan layanan kami.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-base">2</span>
                            Peran & Tanggung Jawab
                        </h2>
                        <div class="space-y-4">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <h3 class="font-bold text-slate-800 mb-2">Bagi Pelajar (Murid):</h3>
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Pelajar wajib menjaga etika belajar, tidak menyebarkan konten modul ke luar platform
                                    secara ilegal, dan mengikuti kuis dengan jujur.
                                </p>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <h3 class="font-bold text-slate-800 mb-2">Bagi Relawan (Pengajar):</h3>
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Relawan bertanggung jawab atas kebenaran materi yang diajarkan dan dilarang menyisipkan
                                    konten yang mengandung unsur SARA, kebencian, atau pornografi.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-base">3</span>
                            Donasi & Transparansi
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Seluruh donasi yang diterima bersifat sukarela dan tidak dapat dikembalikan (*non-refundable*).
                            Kami berkomitmen menggunakan dana tersebut sepenuhnya untuk menunjang operasional platform
                            Ngajar.id dan meningkatkan kualitas pendidikan gratis di Indonesia.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-base">4</span>
                            Hak Kekayaan Intelektual
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Seluruh konten di Ngajar.id, termasuk logo, desain, dan modul, adalah properti intelektual milik
                            Ngajar.id atau penyedia konten terkait. Penggunaan konten di luar platform tanpa izin tertulis
                            adalah pelanggaran hukum.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-base">5</span>
                            Perubahan Ketentuan
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Kami akan memberitahu pengguna
                            melalui email atau pengumuman di platform jika ada perubahan signifikan.
                        </p>
                    </section>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 text-center">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center text-slate-500 hover:text-orange-600 font-bold transition-colors">
                        <span class="material-symbols-rounded mr-2">arrow_back</span>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
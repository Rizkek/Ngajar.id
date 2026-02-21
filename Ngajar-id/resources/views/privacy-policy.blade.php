@extends('layouts.app')

@section('title', 'Kebijakan Privasi - Ngajar.ID')

@section('content')
    <section class="bg-gradient-to-b from-slate-50 to-white pt-24 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Kebijakan <span
                        class="text-brand-600">Privasi</span></h1>
                <p class="text-slate-500">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-200/50 border border-slate-100"
                data-aos="fade-up">
                <div class="prose prose-slate max-w-none space-y-8">
                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-base">1</span>
                            Pendahuluan
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Selamat datang di Ngajar.id. Kami sangat menghargai privasi Anda dan berkomitmen untuk
                            melindungi data pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan,
                            menggunakan, dan melindungi informasi Anda saat menggunakan platform kami.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-base">2</span>
                            Informasi yang Kami Kumpulkan
                        </h2>
                        <p class="text-slate-600 leading-relaxed mb-4">
                            Kami mengumpulkan informasi tertentu untuk memberikan layanan yang lebih baik kepada Anda,
                            termasuk:
                        </p>
                        <ul class="list-disc list-inside text-slate-600 space-y-2 ml-4">
                            <li><strong>Informasi Profil:</strong> Nama lengkap, alamat email, dan foto profil saat Anda
                                mendaftar.</li>
                            <li><strong>Data Transaksi:</strong> Informasi donasi, termasuk nominal dan metode pembayaran
                                (diproses melalui mitra pembayaran pihak ketiga).</li>
                            <li><strong>Konten Belajar:</strong> Data kemajuan belajar, kuis, dan interaksi dalam kelas.
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-base">3</span>
                            Penggunaan Informasi
                        </h2>
                        <p class="text-slate-600 leading-relaxed mb-4">
                            Kami menggunakan informasi Anda untuk:
                        </p>
                        <ul class="list-disc list-inside text-slate-600 space-y-2 ml-4">
                            <li>Menyediakan dan mengelola layanan platform LMS kami.</li>
                            <li>Memproses donasi dan memberikan laporan transparansi.</li>
                            <li>Mengirimkan sertifikat digital setelah kursus selesai.</li>
                            <li>Menghubungi Anda terkait update layanan atau bantuan teknis.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-base">4</span>
                            Keamanan Data
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Kami menerapkan langkah-langika teknis dan organisasi yang sesuai untuk melindungi data pribadi
                            Anda dari akses yang tidak sah, kehilangan, atau kerusakan. Data pembayaran Anda dienkripsi dan
                            diproses melalui gerbang pembayaran (payment gateway) yang aman.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-base">5</span>
                            Kontak Kami
                        </h2>
                        <p class="text-slate-600 leading-relaxed">
                            Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami di <a
                                href="mailto:privacy@ngajar.id"
                                class="text-brand-600 font-bold hover:underline">privacy@ngajar.id</a> atau melalui nomor
                            WhatsApp admin yang tersedia.
                        </p>
                    </section>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 text-center">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center text-slate-500 hover:text-brand-600 font-bold transition-colors">
                        <span class="material-symbols-rounded mr-2">arrow_back</span>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
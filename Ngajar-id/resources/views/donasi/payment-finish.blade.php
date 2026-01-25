@extends('layouts.app')

@section('title', 'Pembayaran Selesai - Ngajar.ID')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="max-w-2xl w-full">
            @if($donasi && $donasi->status === 'paid')
                <!-- Success State -->
                <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center">
                    <!-- Success Icon -->
                    <div
                        class="w-24 h-24 rounded-full bg-green-50 mx-auto mb-6 flex items-center justify-center animate-bounce">
                        <span class="material-symbols-rounded text-6xl text-green-600">check_circle</span>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 mb-3">
                        Terima Kasih! 🎉
                    </h1>
                    <p class="text-lg text-slate-600 mb-8">
                        Donasi Anda telah berhasil diproses
                    </p>

                    <!-- Donation Details Card -->
                    <div class="bg-gradient-to-br from-teal-50 to-blue-50 rounded-2xl p-6 mb-8 text-left">
                        <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-rounded text-teal-600">receipt_long</span>
                            Detail Donasi
                        </h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Nomor Transaksi:</span>
                                <span class="font-bold text-teal-600">{{ $donasi->nomor_transaksi }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Nama Donatur:</span>
                                <span class="font-bold text-slate-900">{{ $donasi->nama }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Jumlah Donasi:</span>
                                <span class="font-bold text-2xl text-teal-600">Rp
                                    {{ number_format($donasi->jumlah, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Waktu:</span>
                                <span class="font-bold text-slate-900">{{ $donasi->tanggal->format('d M Y, H:i') }}</span>
                            </div>
                            @if($donasi->pesan)
                                <div class="pt-3 border-t border-teal-100">
                                    <p class="text-sm text-slate-600 mb-1">Pesan Anda:</p>
                                    <p class="text-slate-800 italic">"{{ $donasi->pesan }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Impact Message -->
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-rounded text-3xl text-amber-600">volunteer_activism</span>
                            <div class="text-left">
                                <h3 class="font-bold text-amber-900 mb-2">Donasi Anda Sangat Berarti!</h3>
                                <p class="text-sm text-amber-800 leading-relaxed">
                                    Dengan donasi sebesar <strong>Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}</strong>,
                                    Anda telah membantu
                                    {{ floor($donasi->jumlah / 150000) > 0 ? floor($donasi->jumlah / 150000) . ' siswa' : 'anak-anak Indonesia' }}
                                    mendapatkan pendidikan yang lebih baik. 🙏
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Notification Info -->
                    @if($donasi->email)
                        <div class="mb-8 text-sm text-slate-600 flex items-center justify-center gap-2">
                            <span class="material-symbols-rounded text-base">mail</span>
                            Invoice telah dikirim ke <strong class="text-slate-900">{{ $donasi->email }}</strong>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('donasi') }}"
                            class="flex-1 py-4 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold rounded-2xl shadow-lg hover:from-teal-700 hover:to-teal-600 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span class="material-symbols-rounded">volunteer_activism</span>
                            Donasi Lagi
                        </a>
                        <a href="{{ route('home') }}"
                            class="flex-1 py-4 bg-white border-2 border-gray-200 text-slate-700 font-bold rounded-2xl hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-rounded">home</span>
                            Kembali ke Beranda
                        </a>
                    </div>

                    <!-- Share -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <p class="text-sm text-slate-600 mb-4">Ajak teman Anda untuk berdonasi juga:</p>
                        <div class="flex gap-3 justify-center">
                            <button onclick="shareToWhatsApp()"
                                class="w-12 h-12 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                            </button>
                            <button onclick="shareToTwitter()"
                                class="w-12 h-12 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </button>
                            <button onclick="shareToFacebook()"
                                class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors">
                                <span class="material-symbols-rounded">facebook</span>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Pending/Failed State -->
                <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center">
                    <div class="w-24 h-24 rounded-full bg-amber-50 mx-auto mb-6 flex items-center justify-center">
                        <span class="material-symbols-rounded text-6xl text-amber-600">pending</span>
                    </div>

                    <h1 class="text-3xl font-black text-slate-900 mb-3">
                        Pembayaran Sedang Diproses
                    </h1>
                    <p class="text-slate-600 mb-8">
                        Mohon tunggu beberapa saat hingga pembayaran Anda dikonfirmasi.
                    </p>

                    @if($donasi)
                        <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                            <p class="text-sm text-slate-600 mb-2">Nomor Transaksi:</p>
                            <p class="font-bold text-lg text-slate-900">{{ $donasi->nomor_transaksi }}</p>
                        </div>
                    @endif

                    <a href="{{ route('donasi') }}"
                        class="inline-block py-4 px-8 bg-teal-600 text-white font-bold rounded-2xl hover:bg-teal-700 transition-all">
                        Kembali ke Halaman Donasi
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function shareToWhatsApp() {
            const text = 'Saya baru saja berdonasi di Ngajar.ID untuk membantu pendidikan anak Indonesia. Yuk, ikutan berbagi! 🎓';
            const url = '{{ route("donasi") }}';
            window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
        }

        function shareToTwitter() {
            const text = 'Saya baru saja berdonasi di @NgajarID untuk membantu pendidikan anak Indonesia. Yuk, ikutan berbagi! 🎓';
            const url = '{{ route("donasi") }}';
            window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
        }

        function shareToFacebook() {
            const url = '{{ route("donasi") }}';
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        }
    </script>
@endsection
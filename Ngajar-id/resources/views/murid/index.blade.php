@extends('layouts.dashboard')

@section('title', 'Dashboard Murid - Ngajar.ID')
@section('header_title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Halo, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-slate-600 mt-1">"Pendidikan adalah senjata paling ampuh untuk mengubah dunia." - Nelson Mandela</p>
    </div>

    <!-- Stats & Gamification Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- XP & Level Card -->
        <div
            class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-rounded text-9xl">military_tech</span>
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-indigo-200 text-sm font-medium uppercase tracking-wider">Level Saat Ini</p>
                        <h2 class="text-4xl font-black mt-1">{{ $userStats['level'] }}</h2>
                    </div>
                    <div class="bg-indigo-500/30 p-2 rounded-lg backdrop-blur-sm">
                        <span class="material-symbols-rounded text-yellow-300 text-3xl">workspace_premium</span>
                    </div>
                </div>

                <div class="mb-2 flex justify-between text-sm">
                    <span class="font-bold text-indigo-100">{{ $userStats['xp'] }} XP</span>
                    <span class="text-indigo-300">Target: {{ $userStats['xp_next_level'] }} XP</span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-indigo-900/50 rounded-full h-3">
                    <div class="bg-yellow-400 h-3 rounded-full transition-all duration-1000"
                        style="width: {{ ($userStats['xp'] / $userStats['xp_next_level']) * 100 }}%"></div>
                </div>
                <p class="text-xs text-indigo-300 mt-2">Dapatkan +50 XP setiap menyelesaikan materi!</p>
            </div>
        </div>

        <!-- Token Wallet Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-rounded text-amber-500 text-3xl">token</span>
                    <h3 class="text-lg font-bold text-slate-700">Saldo Token</h3>
                </div>
                <h2 class="text-4xl font-black text-slate-900">{{ number_format($userStats['token_balance']) }}</h2>
                <p class="text-slate-500 text-sm mt-1">Gunakan untuk membeli modul premium.</p>
            </div>
            <button onclick="openTopupModal()"
                class="mt-4 w-full py-2 bg-amber-50 text-amber-700 font-bold rounded-xl hover:bg-amber-100 transition text-center text-sm border border-amber-200 flex items-center justify-center gap-2">
                <span class="material-symbols-rounded text-lg">add_circle</span>
                Top Up Token
            </button>
        </div>

        <!-- Weekly Activity Chart (Simplified) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative">
            <h3 class="text-lg font-bold text-slate-700 mb-4">Aktivitas Belajar</h3>
            <div class="flex items-end justify-between h-32 gap-2">
                @foreach($activityChart['data'] as $index => $val)
                    <div class="w-full bg-teal-100 rounded-t-lg relative group transition-all hover:bg-teal-200"
                        style="height: {{ $val * 8 }}%">
                        <div
                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                            {{ $val }} Jam
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-xs text-slate-400 font-medium">
                @foreach($activityChart['labels'] as $label)
                    <span>{{ $label }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column: Continue Learning -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Continue Learning Card -->
            @if($lastClass)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-teal-50/50">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span class="material-symbols-rounded text-teal-600">history</span>
                            Lanjutkan Belajar
                        </h2>
                        <span class="text-xs bg-teal-100 text-teal-700 px-2 py-1 rounded font-bold">Terakhir Diakses</span>
                    </div>
                    <div class="p-6 md:flex gap-6 items-center">
                        <!-- Class Thumbnail -->
                        <div
                            class="w-full md:w-48 h-32 bg-gray-200 rounded-xl flex-shrink-0 relative overflow-hidden mb-4 md:mb-0">
                            @if($lastClass->thumbnail)
                                <img src="{{ asset('storage/' . $lastClass->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300">
                                    <span class="material-symbols-rounded text-slate-400 text-4xl">school</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition"></div>
                        </div>

                        <!-- Class Info -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-900 mb-1 group-hover:text-teal-600 transition">
                                {{ $lastClass->judul }}
                            </h3>
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ $lastClass->deskripsi }}</p>

                            <div class="flex items-center gap-4 mb-4 text-sm text-slate-600">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-lg text-slate-400">person</span>
                                    {{ $lastClass->pengajar->name }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-rounded text-lg text-slate-400">menu_book</span>
                                    12 Materi
                                </span>
                            </div>

                            <a href="{{ route('belajar.show', ['kelas_id' => $lastClass->kelas_id]) }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition shadow-lg shadow-teal-200">
                                <span>Lanjut Belajar</span>
                                <span class="material-symbols-rounded text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State for New Users -->
                <div class="bg-indigo-50 rounded-2xl p-8 text-center border-2 border-dashed border-indigo-200">
                    <div
                        class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-600">
                        <span class="material-symbols-rounded text-4xl">rocket_launch</span>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-900 mb-2">Mulai Petualangan Belajarmu!</h3>
                    <p class="text-indigo-700 mb-6 max-w-md mx-auto">Anda belum mengikuti kelas apapun. Yuk jelajahi katalog
                        kelas kami dan temukan skill baru.</p>
                    <a href="{{ route('murid.katalog') }}"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg transition inline-flex items-center gap-2">
                        <span class="material-symbols-rounded">manage_search</span>
                        Jelajah Kelas
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Column: Recommendations -->
        <div class="space-y-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-slate-800 text-lg">Rekomendasi Kelas</h3>
                <a href="{{ route('murid.katalog') }}" class="text-sm text-teal-600 hover:underline">Lihat Semua</a>
            </div>

            @foreach($recommendedClasses as $kelas)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition group relative">
                    <div class="flex gap-4">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center text-gray-400">
                            @if($kelas->thumbnail)
                                <img src="{{ asset('storage/' . $kelas->thumbnail) }}"
                                    class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="material-symbols-rounded text-2xl">image</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-900 text-sm truncate group-hover:text-teal-600 transition">
                                {{ $kelas->judul }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-1 mb-2">{{ $kelas->pengajar->name }}</p>

                            <form action="{{ route('murid.katalog.join', $kelas->kelas_id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                                    <span class="material-symbols-rounded text-sm">add</span> Gabung
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Quick Link to Modules -->
            <a href="{{ route('murid.materi') }}"
                class="block p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 hover:border-amber-300 transition group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 group-hover:scale-110 transition">
                        <span class="material-symbols-rounded">library_books</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-900">Modul Tambahan</h4>
                        <p class="text-xs text-amber-700">Explorasi e-book & cheat sheet</p>
                    </div>
                    <span
                        class="material-symbols-rounded text-amber-400 ml-auto group-hover:translate-x-1 transition">chevron_right</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Topup Modal -->
    <div id="topupModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-100 p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Top Up Token</h2>
                    <p class="text-sm text-slate-500 mt-1">Pilih paket token yang sesuai kebutuhan Anda</p>
                </div>
                <button onclick="closeTopupModal()" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <span class="material-symbols-rounded text-gray-500">close</span>
                </button>
            </div>

            <!-- Current Balance -->
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mx-6 mt-6 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-rounded text-amber-600 text-2xl">account_balance_wallet</span>
                        <div>
                            <p class="text-xs text-amber-700 font-medium">Saldo Token Saat Ini</p>
                            <p class="text-2xl font-black text-amber-900">{{ number_format($userStats['token_balance']) }}
                                Token</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Token Packages -->
            <div class="p-6 space-y-4">
                <h3 class="font-bold text-slate-700 mb-4">Pilih Paket:</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Package 1: Starter -->
                    <div class="border-2 border-gray-200 hover:border-teal-500 rounded-xl p-4 cursor-pointer transition group relative"
                        onclick="selectPackage(10000, 50)">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">Paket Starter</h4>
                                <p class="text-xs text-slate-500">Cocok untuk pemula</p>
                            </div>
                            <span
                                class="material-symbols-rounded text-gray-300 group-hover:text-teal-500 transition">radio_button_unchecked</span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="material-symbols-rounded text-amber-500 text-xl">token</span>
                            <span class="text-3xl font-black text-slate-900">50</span>
                            <span class="text-slate-500 text-sm">Token</span>
                        </div>
                        <div class="text-teal-600 font-bold text-xl">Rp 10.000</div>
                    </div>

                    <!-- Package 2: Popular -->
                    <div class="border-2 border-teal-500 bg-teal-50 rounded-xl p-4 cursor-pointer transition group relative"
                        onclick="selectPackage(25000, 150)">
                        <div
                            class="absolute -top-2 -right-2 bg-gradient-to-r from-teal-500 to-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            POPULER
                        </div>
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">Paket Pro</h4>
                                <p class="text-xs text-teal-700">Hemat 20%! + Bonus XP</p>
                            </div>
                            <span class="material-symbols-rounded text-teal-500 transition">check_circle</span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="material-symbols-rounded text-amber-500 text-xl">token</span>
                            <span class="text-3xl font-black text-slate-900">150</span>
                            <span class="text-slate-500 text-sm">Token</span>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">+125 Token
                                bonus!</span>
                        </div>
                        <div class="text-teal-600 font-bold text-xl">Rp 25.000</div>
                    </div>

                    <!-- Package 3: Premium -->
                    <div class="border-2 border-gray-200 hover:border-purple-500 rounded-xl p-4 cursor-pointer transition group relative"
                        onclick="selectPackage(50000, 350)">
                        <div
                            class="absolute -top-2 -right-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            BEST DEAL
                        </div>
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">Paket Premium</h4>
                                <p class="text-xs text-slate-500">Hemat 30%! + Bonus XP</p>
                            </div>
                            <span
                                class="material-symbols-rounded text-gray-300 group-hover:text-purple-500 transition">radio_button_unchecked</span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="material-symbols-rounded text-amber-500 text-xl">token</span>
                            <span class="text-3xl font-black text-slate-900">350</span>
                            <span class="text-slate-500 text-sm">Token</span>
                            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">+300 Token
                                bonus!</span>
                        </div>
                        <div class="text-purple-600 font-bold text-xl">Rp 50.000</div>
                    </div>

                    <!-- Package 4: Ultimate -->
                    <div class="border-2 border-gray-200 hover:border-amber-500 rounded-xl p-4 cursor-pointer transition group relative bg-gradient-to-br from-amber-50 to-orange-50"
                        onclick="selectPackage(100000, 800)">
                        <div
                            class="absolute -top-2 -right-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            VIP
                        </div>
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">Paket Ultimate</h4>
                                <p class="text-xs text-amber-700">Hemat 40%! + Bonus XP</p>
                            </div>
                            <span
                                class="material-symbols-rounded text-gray-300 group-hover:text-amber-500 transition">radio_button_unchecked</span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="material-symbols-rounded text-amber-500 text-xl">token</span>
                            <span class="text-3xl font-black text-slate-900">800</span>
                            <span class="text-slate-500 text-sm">Token</span>
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-bold">+700 Token
                                bonus!</span>
                        </div>
                        <div class="text-amber-600 font-bold text-xl">Rp 100.000</div>
                    </div>
                </div>

                <!-- Selected Package Info -->
                <div id="selectedPackageInfo" class="hidden bg-teal-50 border border-teal-200 rounded-xl p-4 mt-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-teal-600 text-2xl">shopping_cart</span>
                        <div class="flex-1">
                            <p class="text-sm text-teal-700 font-medium mb-2">Paket Terpilih:</p>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-slate-900" id="selectedTokens">-</p>
                                    <p class="text-xs text-slate-500">Total token yang didapat</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-teal-600" id="selectedPrice">Rp 0</p>
                                    <p class="text-xs text-slate-500">Total pembayaran</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <button id="payButton" disabled onclick="processPayment()"
                    class="w-full py-3 bg-gray-300 text-gray-500 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2 mt-6 transition disabled:cursor-not-allowed">
                    <span class="material-symbols-rounded">lock</span>
                    Pilih Paket Terlebih Dahulu
                </button>

                <!-- Payment Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <div class="flex gap-3">
                        <span class="material-symbols-rounded text-blue-600">info</span>
                        <div class="text-xs text-blue-800">
                            <p class="font-bold mb-1">Metode Pembayaran:</p>
                            <p>Pembayaran akan diproses melalui <strong>Midtrans</strong>. Anda dapat menggunakan berbagai
                                metode seperti Transfer Bank, E-Wallet (GoPay, OVO, Dana), Kartu Kredit, dan lainnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedAmount = 0;
        let selectedTokens = 0;

        function openTopupModal() {
            const modal = document.getElementById('topupModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeTopupModal() {
            const modal = document.getElementById('topupModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            selectedAmount = 0;
            selectedTokens = 0;
            document.getElementById('selectedPackageInfo').classList.add('hidden');
            document.getElementById('payButton').disabled = true;
            document.getElementById('payButton').className = 'w-full py-3 bg-gray-300 text-gray-500 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2 mt-6 transition';
            document.getElementById('payButton').innerHTML = '<span class="material-symbols-rounded">lock</span> Pilih Paket Terlebih Dahulu';
        }

        function selectPackage(amount, tokens) {
            selectedAmount = amount;
            selectedTokens = tokens;

            // Update UI
            document.getElementById('selectedTokens').textContent = tokens + ' Token';
            document.getElementById('selectedPrice').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('selectedPackageInfo').classList.remove('hidden');

            // Enable pay button
            const payButton = document.getElementById('payButton');
            payButton.disabled = false;
            payButton.className = 'w-full py-3 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 mt-6 transition shadow-lg';
            payButton.innerHTML = '<span class="material-symbols-rounded">payment</span> Bayar Sekarang - Rp ' + amount.toLocaleString('id-ID');
        }

        function processPayment() {
            if (selectedAmount === 0 || selectedTokens === 0) {
                alert('Silakan pilih paket terlebih dahulu!');
                return;
            }

            const payButton = document.getElementById('payButton');
            const originalText = payButton.innerHTML;
            payButton.disabled = true;
            payButton.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

            fetch('{{ route("topup.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    amount: selectedAmount,
                    tokens: selectedTokens
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.invoice_url) {
                        // Redirect ke Xendit Invoice
                        window.location.href = data.invoice_url;
                    } else {
                        alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                        payButton.disabled = false;
                        payButton.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.innerHTML = originalText;
                });
        }

        // Close modal on outside click
        document.getElementById('topupModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeTopupModal();
            }
        });
    </script>


@endsection
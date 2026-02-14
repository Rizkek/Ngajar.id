@extends('layouts.dashboard')

@section('title', 'Pustaka Belajar - Ngajar.ID')@section('content')
    <div class="container-fluid px-4 pb-12" x-data="{ activeTab: 'all' }">
        <!-- Header Hero -->
        <div class="bg-gradient-to-r from-teal-600 to-emerald-600 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-rounded text-[150px]">local_library</span>
            </div>
            <div class="relative z-10 max-w-2xl">
                <h1 class="text-3xl font-black mb-2">Pustaka Belajar (Resources)</h1>
                <p class="text-teal-100 text-lg mb-6">Koleksi e-book, cheat sheet, source code, dan aset pendukung untuk mempercepat proses belajarmu. Akses seumur hidup!</p>

                <div class="flex gap-4">
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 flex items-center gap-2">
                        <span class="material-symbols-rounded text-yellow-300">verified</span>
                        <span class="font-bold">Original Content</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 flex items-center gap-2">
                        <span class="material-symbols-rounded text-cyan-300">download</span>
                        <span class="font-bold">Instant Download</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="flex overflow-x-auto pb-4 gap-2 mb-6 scrollbar-hide">
            <button @click="activeTab = 'all'" 
                :class="activeTab === 'all' ? 'bg-teal-600 text-white shadow-lg shadow-teal-500/30' : 'bg-white text-slate-600 hover:bg-gray-50 border border-gray-200'"
                class="px-6 py-2.5 rounded-full font-bold whitespace-nowrap transition flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">apps</span>
                Semua
            </button>
            <button @click="activeTab = 'ebook'" 
                :class="activeTab === 'ebook' ? 'bg-teal-600 text-white shadow-lg shadow-teal-500/30' : 'bg-white text-slate-600 hover:bg-gray-50 border border-gray-200'"
                class="px-6 py-2.5 rounded-full font-bold whitespace-nowrap transition flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">book_2</span>
                E-Book
            </button>
            <button @click="activeTab = 'cheatsheet'" 
                :class="activeTab === 'cheatsheet' ? 'bg-teal-600 text-white shadow-lg shadow-teal-500/30' : 'bg-white text-slate-600 hover:bg-gray-50 border border-gray-200'"
                class="px-6 py-2.5 rounded-full font-bold whitespace-nowrap transition flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">description</span>
                Cheat Sheet
            </button>
            <button @click="activeTab = 'sourcecode'" 
                :class="activeTab === 'sourcecode' ? 'bg-teal-600 text-white shadow-lg shadow-teal-500/30' : 'bg-white text-slate-600 hover:bg-gray-50 border border-gray-200'"
                class="px-6 py-2.5 rounded-full font-bold whitespace-nowrap transition flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">code</span>
                Source Code
            </button>
        </div>

        <!-- Resources Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Item 1: Laravel Cheat Sheet -->
            <div x-show="activeTab === 'all' || activeTab === 'cheatsheet'" class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition group overflow-hidden flex flex-col h-full">
                <div class="h-48 bg-red-50 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-pattern-grid opacity-10"></div>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png" class="h-24 w-24 object-contain shadow-lg rounded-xl transform group-hover:scale-110 transition duration-500">
                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-slate-700 shadow-sm flex items-center gap-1">
                        <span class="material-symbols-rounded text-sm text-red-500">description</span> PDF
                    </span>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="mb-auto">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-red-600 transition">Laravel 11 Cheat Sheet</h3>
                        <p class="text-sm text-slate-500 line-clamp-2">Rangkuman syntax lengkap Laravel 11 mulai dari routing, controller, hingga eloquent.</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <span class="block text-xs text-slate-400">Harga</span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-rounded text-amber-500 text-sm">token</span>
                                <span class="font-black text-slate-700">Gratis</span>
                            </div>
                        </div>
                        <button class="px-4 py-2 bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white rounded-xl font-bold text-sm transition flex items-center gap-1">
                            <span class="material-symbols-rounded text-lg">download</span>
                            <span>Unduh</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Item 2: Tailwind CSS Guide -->
            <div x-show="activeTab === 'all' || activeTab === 'ebook'" class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition group overflow-hidden flex flex-col h-full">
                <div class="h-48 bg-cyan-50 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-pattern-dots opacity-10"></div>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d5/Tailwind_CSS_Logo.svg" class="h-24 w-24 object-contain shadow-lg rounded-xl transform group-hover:scale-110 transition duration-500">
                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-slate-700 shadow-sm flex items-center gap-1">
                        <span class="material-symbols-rounded text-sm text-cyan-500">book_2</span> E-Book
                    </span>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="mb-auto">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-cyan-600 transition">Mastering Tailwind CSS</h3>
                        <p class="text-sm text-slate-500 line-clamp-2">Panduan mendalam membuat desain modern dengan utility-first framework.</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <span class="block text-xs text-slate-400">Harga</span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-rounded text-amber-500 text-sm">token</span>
                                <span class="font-black text-slate-900">150</span>
                            </div>
                        </div>
                        <button onclick="confirmPurchase('Tailwind Guide', 150)" class="px-4 py-2 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-xl font-bold text-sm transition flex items-center gap-1">
                            <span class="material-symbols-rounded text-lg">shopping_cart</span>
                            <span>Beli</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Item 3: POS System Source Code -->
            <div x-show="activeTab === 'all' || activeTab === 'sourcecode'" class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition group overflow-hidden flex flex-col h-full">
                <div class="h-48 bg-slate-800 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-slate-900/50"></div>
                    <span class="material-symbols-rounded text-6xl text-white opacity-80 group-hover:scale-110 transition duration-500">point_of_sale</span>
                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-slate-700 shadow-sm flex items-center gap-1">
                        <span class="material-symbols-rounded text-sm text-purple-500">code</span> Source
                    </span>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="mb-auto">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-purple-600 transition">Point of Sale (POS) App</h3>
                        <p class="text-sm text-slate-500 line-clamp-2">Full source code aplikasi kasir berbasis Laravel & Livewire. Siap pakai!</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <span class="block text-xs text-slate-400">Harga</span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-rounded text-amber-500 text-sm">token</span>
                                <span class="font-black text-slate-900">500</span>
                            </div>
                        </div>
                        <button onclick="confirmPurchase('POS Source Code', 500)" class="px-4 py-2 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-xl font-bold text-sm transition flex items-center gap-1">
                            <span class="material-symbols-rounded text-lg">shopping_cart</span>
                            <span>Beli</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Item 4: Freelance Starter Kit -->
            <div x-show="activeTab === 'all' || activeTab === 'ebook'" class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition group overflow-hidden flex flex-col h-full">
                <div class="h-48 bg-indigo-50 relative flex items-center justify-center overflow-hidden">
                    <span class="material-symbols-rounded text-6xl text-indigo-400 group-hover:scale-110 transition duration-500">work_history</span>
                     <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-slate-700 shadow-sm flex items-center gap-1">
                        <span class="material-symbols-rounded text-sm text-indigo-500">folder_zip</span> Kit
                    </span>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="mb-auto">
                        <h3 class="font-bold text-slate-900 text-lg mb-1 group-hover:text-indigo-600 transition">Freelancer Starter Kit</h3>
                        <p class="text-sm text-slate-500 line-clamp-2">Template invoice, proposal, kontrak kerja, dan hitungan rate card.</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <span class="block text-xs text-slate-400">Harga</span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-rounded text-amber-500 text-sm">token</span>
                                <span class="font-black text-slate-900">200</span>
                            </div>
                        </div>
                        <button onclick="confirmPurchase('Freelance Kit', 200)" class="px-4 py-2 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-xl font-bold text-sm transition flex items-center gap-1">
                            <span class="material-symbols-rounded text-lg">shopping_cart</span>
                            <span>Beli</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Konfirmasi Beli -->
    <div id="purchaseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl transform transition-all scale-95 opacity-0" id="purchaseModalContent">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-3xl text-amber-600">shopping_cart_checkout</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Beli Resource Ini?</h3>
                <p class="text-sm text-slate-500" id="purchaseItemName">Nama Item</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-slate-500">Harga</span>
                    <span class="font-bold text-amber-600" id="purchaseItemPrice">0 Token</span>
                </div>
                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-gray-200">
                    <span class="text-sm text-slate-500">Saldo Anda</span>
                    <span class="font-bold text-teal-600">{{ number_format(auth()->user()->getSaldoToken()) }} Token</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="closePurchaseModal()" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-slate-600 font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button onclick="processPurchase()" class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition flex items-center justify-center gap-2">
                    Beli Sekarang
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmPurchase(name, price) {
            document.getElementById('purchaseItemName').textContent = name;
            document.getElementById('purchaseItemPrice').textContent = price + ' Token';

            const modal = document.getElementById('purchaseModal');
            const content = document.getElementById('purchaseModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closePurchaseModal() {
            const modal = document.getElementById('purchaseModal');
            const content = document.getElementById('purchaseModalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 200);
        }

        function processPurchase() {
            alert('Fitur pembelian resource ini sedang dalam pengembangan! Nantikan update selanjutnya.');
            closePurchaseModal();
        }
    </script>
@endsection
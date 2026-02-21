@extends('layouts.dashboard')

@section('title', 'Kelola Produk Digital - Pengajar')

@section('content')
    <div class="container-fluid px-4 pb-12">
        <!-- Header -->
        <div data-aos="fade-down"
            class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-rounded text-[150px]">sell</span>
            </div>
            <div class="relative z-10 max-w-3xl">
                <h1 class="text-3xl font-black mb-2">Kelola Produk Digital</h1>
                <p class="text-purple-100 text-lg mb-6">Upload dan jual e-book, source code, template, dan cheat sheet Anda.
                    Dapatkan passive income dari karya digital!</p>

                <div class="flex gap-4 flex-wrap">
                    <div
                        class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 flex items-center gap-2">
                        <span class="material-symbols-rounded text-yellow-300">payments</span>
                        <span class="font-bold">Passive Income</span>
                    </div>
                    <div
                        class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 flex items-center gap-2">
                        <span class="material-symbols-rounded text-green-300">all_inclusive</span>
                        <span class="font-bold">Akses Selamanya</span>
                    </div>
                    <div
                        class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30 flex items-center gap-2">
                        <span class="material-symbols-rounded text-cyan-300">workspace_premium</span>
                        <span class="font-bold">Original Content</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div data-aos="fade-up" data-aos-delay="100"
                class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-purple-600">inventory_2</span>
                    </div>
                    <span class="text-3xl font-black text-slate-900">4</span>
                </div>
                <p class="text-sm font-bold text-slate-600">Total Produk</p>
                <p class="text-xs text-slate-400 mt-1">2 Aktif, 2 Draft</p>
            </div>

            <div data-aos="fade-up" data-aos-delay="200"
                class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-green-600">download</span>
                    </div>
                    <span class="text-3xl font-black text-green-600">127</span>
                </div>
                <p class="text-sm font-bold text-slate-600">Total Unduhan</p>
                <p class="text-xs text-slate-400 mt-1">+23 bulan ini</p>
            </div>

            <div data-aos="fade-up" data-aos-delay="300"
                class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-amber-600">token</span>
                    </div>
                    <span class="text-3xl font-black text-amber-600">1.2K</span>
                </div>
                <p class="text-sm font-bold text-slate-600">Token Terjual</p>
                <p class="text-xs text-slate-400 mt-1">≈ Rp 120.000</p>
            </div>

            <div data-aos="fade-up" data-aos-delay="400"
                class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-blue-600">star</span>
                    </div>
                    <span class="text-3xl font-black text-blue-600">4.8</span>
                </div>
                <p class="text-sm font-bold text-slate-600">Rating Rata-rata</p>
                <p class="text-xs text-slate-400 mt-1">Dari 47 review</p>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 mb-1">Produk Saya</h2>
                <p class="text-slate-500 text-sm">Kelola semua produk digital Anda di sini</p>
            </div>
            <button
                class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/40 transition flex items-center gap-2">
                <span class="material-symbols-rounded">add_circle</span>
                <span>Upload Produk Baru</span>
            </button>
        </div>

        <!-- Products List -->
        <div class="space-y-4">

            <!-- Product 1: Laravel Cheat Sheet -->
            <div data-aos="fade-up" data-aos-delay="100"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group">
                <div class="flex">
                    <div class="w-48 h-48 bg-red-50 flex items-center justify-center relative overflow-hidden shrink-0">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png"
                            class="h-24 w-24 object-contain">
                        <div
                            class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                            <span class="material-symbols-rounded text-sm">check_circle</span> Aktif
                        </div>
                    </div>

                    <div class="flex-1 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-black text-slate-900">Laravel 11 Cheat Sheet</h3>
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded">PDF</span>
                                </div>
                                <p class="text-slate-500 text-sm">Rangkuman syntax lengkap Laravel 11 dari routing hingga
                                    eloquent</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-400 mb-1">Harga</p>
                                <div class="flex items-center gap-1 justify-end">
                                    <span class="material-symbols-rounded text-amber-500">token</span>
                                    <span class="text-2xl font-black text-slate-900">Gratis</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-6 mb-4">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-rounded text-green-500">download</span>
                                <span class="font-semibold text-slate-700">237 Unduhan</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-rounded text-yellow-500">star</span>
                                <span class="font-semibold text-slate-700">4.9 (18 review)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-rounded text-blue-500">schedule</span>
                                <span class="font-semibold text-slate-700">Upload 12 Jan 2026</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button
                                class="flex-1 py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg font-bold transition flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded text-lg">edit</span>
                                <span>Edit</span>
                            </button>
                            <button
                                class="flex-1 py-2 px-4 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg font-bold transition flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded text-lg">analytics</span>
                                <span>Lihat Statistik</span>
                            </button>
                            <button
                                class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-bold transition">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2: POS System -->
            <div data-aos="fade-up" data-aos-delay="200"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group">
                <div class="flex">
                    <div class="w-48 h-48 bg-slate-800 flex items-center justify-center relative overflow-hidden shrink-0">
                        <span class="material-symbols-rounded text-white text-6xl">point_of_sale</span>
                        <div
                            class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                            <span class="material-symbols-rounded text-sm">check_circle</span> Aktif
                        </div>
                    </div>

                    <div class="flex-1 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-black text-slate-900">Point of Sale (POS) App</h3>
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded">Source
                                        Code</span>
                                </div>
                                <p class="text-slate-500 text-sm">Full source code aplikasi kasir berbasis Laravel &
                                    Livewire</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-400 mb-1">Harga</p>
                                <div class="flex items-center gap-1 justify-end">
                                    <span class="material-symbols-rounded text-amber-500">token</span>
                                    <span class="text-2xl font-black text-slate-900">500</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-6 mb-4">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-rounded text-green-500">download</span>
                                <span class="font-semibold text-slate-700">18 Unduhan</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-rounded text-yellow-500">star</span>
                                <span class="font-semibold text-slate-700">5.0 (5 review)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-rounded text-blue-500">schedule</span>
                                <span class="font-semibold text-slate-700">Upload 8 Feb 2026</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button
                                class="flex-1 py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg font-bold transition flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded text-lg">edit</span>
                                <span>Edit</span>
                            </button>
                            <button
                                class="flex-1 py-2 px-4 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg font-bold transition flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded text-lg">analytics</span>
                                <span>Lihat Statistik</span>
                            </button>
                            <button
                                class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-bold transition">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State (Jika tidak ada produk) -->
            <!-- Uncomment ini jika mau pakai empty state -->
            <!--
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                            <div class="w-24 h-24 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="material-symbols-rounded text-purple-400 text-5xl">add_box</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Produk Digital</h3>
                            <p class="text-slate-500 mb-6 max-w-md mx-auto">Mulai monetisasi skill Anda dengan menjual e-book, source code, atau template.</p>
                            <button class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold shadow-lg shadow-purple-500/30 transition flex items-center gap-2 mx-auto">
                                <span class="material-symbols-rounded">cloud_upload</span>
                                <span>Upload Produk Pertama</span>
                            </button>
                        </div>
                        -->
        </div>
    </div>
@endsection
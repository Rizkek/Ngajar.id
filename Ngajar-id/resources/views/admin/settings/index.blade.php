@extends('layouts.dashboard')

@section('title', 'Pengaturan Platform - Admin')

@section('content')
    <div class="container-fluid px-4 max-w-5xl" x-data="{ activeTab: 'general' }">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Pengaturan Platform</h1>
            <p class="text-slate-600">Konfigurasi sistem Ngajar.ID</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <span class="material-symbols-rounded">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabs Navigation -->
        <div class="flex space-x-1 border-b border-gray-200 mb-6 overflow-x-auto">
            <button @click="activeTab = 'general'"
                :class="activeTab === 'general' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                <span class="material-symbols-rounded text-lg">settings</span>
                Umum
            </button>
            <button @click="activeTab = 'social'"
                :class="activeTab === 'social' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                <span class="material-symbols-rounded text-lg">share</span>
                Media Sosial
            </button>
            <button @click="activeTab = 'payment'"
                :class="activeTab === 'payment' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                <span class="material-symbols-rounded text-lg">payments</span>
                Payment Gateway
            </button>
            <button @click="activeTab = 'rules'"
                :class="activeTab === 'rules' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                <span class="material-symbols-rounded text-lg">gavel</span>
                Kebijakan & Syarat
            </button>
        </div>

        <!-- Tab Contents -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 min-h-[400px]">

            <!-- General Settings -->
            <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <h2 class="text-xl font-bold text-slate-800 mb-6 pb-4 border-b border-gray-100">Informasi Dasar Situs</h2>
                <form action="{{ route('admin.settings.updateGeneral') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Situs</label>
                            <input type="text" name="site_name"
                                value="{{ $settings['general']['site_name'] ?? 'Ngajar.ID' }}" Required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tagline</label>
                            <input type="text" name="site_tagline" value="{{ $settings['general']['site_tagline'] ?? '' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email Kontak</label>
                            <input type="email" name="contact_email"
                                value="{{ $settings['general']['contact_email'] ?? '' }}" Required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Telepon</label>
                            <input type="text" name="contact_phone"
                                value="{{ $settings['general']['contact_phone'] ?? '' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Kantor</label>
                            <textarea name="contact_address" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500">{{ $settings['general']['contact_address'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-6 py-3 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 transition-all shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Social Settings -->
            <div x-show="activeTab === 'social'" style="display: none;"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0">
                <h2 class="text-xl font-bold text-slate-800 mb-6 pb-4 border-b border-gray-100">Tautan Media Sosial</h2>
                <form action="{{ route('admin.settings.updateSocial') }}" method="POST" class="space-y-6 max-w-2xl">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Facebook URL</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 material-symbols-rounded text-slate-400">public</span>
                            <input type="url" name="facebook_url" value="{{ $settings['social']['facebook_url'] ?? '' }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500"
                                placeholder="https://facebook.com/...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Twitter / X URL</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 material-symbols-rounded text-slate-400">public</span>
                            <input type="url" name="twitter_url" value="{{ $settings['social']['twitter_url'] ?? '' }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500"
                                placeholder="https://twitter.com/...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Instagram URL</label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-3.5 material-symbols-rounded text-slate-400">photo_camera</span>
                            <input type="url" name="instagram_url" value="{{ $settings['social']['instagram_url'] ?? '' }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500"
                                placeholder="https://instagram.com/...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">YouTube URL</label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-3.5 material-symbols-rounded text-slate-400">smart_display</span>
                            <input type="url" name="youtube_url" value="{{ $settings['social']['youtube_url'] ?? '' }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500"
                                placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-6 py-3 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 transition-all shadow-md">
                            Simpan Tautan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payment Settings -->
            <div x-show="activeTab === 'payment'" style="display: none;"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0">
                <h2 class="text-xl font-bold text-slate-800 mb-6 pb-4 border-b border-gray-100">Konfigurasi Pembayaran
                    (Sensitive)</h2>
                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-lg mb-6 flex items-start gap-3">
                    <span class="material-symbols-rounded mt-0.5">warning</span>
                    <div>
                        <p class="font-bold">Perhatian!</p>
                        <p class="text-sm">Perubahan di sini akan langsung mengubah file <code>.env</code> server. Pastikan
                            data yang dimasukkan benar agar sistem pembayaran tetap berjalan.</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.updatePayment') }}" method="POST" class="space-y-8 max-w-3xl">
                    @csrf

                    <!-- Midtrans -->
                    <div>
                        <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <span class="material-symbols-rounded text-blue-600">credit_card</span> Midtrans Config
                        </h3>
                        <div class="space-y-4 p-5 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Server Key</label>
                                <input type="password" name="midtrans_server_key" value="{{ env('MIDTRANS_SERVER_KEY') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-brand-500"
                                    placeholder="SB-Mid-server-...">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Client Key</label>
                                <input type="text" name="midtrans_client_key" value="{{ env('MIDTRANS_CLIENT_KEY') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-brand-500"
                                    placeholder="SB-Mid-client-...">
                            </div>
                            <div class="flex items-center gap-3 mt-2">
                                <input type="checkbox" id="is_prod" name="midtrans_is_production" value="1" {{ env('MIDTRANS_IS_PRODUCTION') ? 'checked' : '' }}
                                    class="w-5 h-5 text-brand-600 rounded focus:ring-brand-500">
                                <label for="is_prod" class="font-medium text-slate-700">Mode Produksi (Live)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Xendit (Optional) -->
                    <div>
                        <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <span class="material-symbols-rounded text-blue-600">account_balance_wallet</span> Xendit Config
                            (Opsional)
                        </h3>
                        <div class="space-y-4 p-5 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Secret Key</label>
                                <input type="password" name="xendit_secret_key" value="{{ env('XENDIT_SECRET_KEY') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-brand-500"
                                    placeholder="xnd_development_...">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-6 py-3 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 transition-all shadow-md">
                            Update API Keys
                        </button>
                    </div>
                </form>
            </div>

            <!-- Rules Settings -->
            <div x-show="activeTab === 'rules'" style="display: none;" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <h2 class="text-xl font-bold text-slate-800 mb-6 pb-4 border-b border-gray-100">Kebijakan Platform</h2>
                <form action="{{ route('admin.settings.updateRules') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Privacy Policy (Kebijakan
                            Privasi)</label>
                        <textarea name="privacy_policy" rows="10"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 font-mono text-sm"
                            placeholder="Tulis kebijakan privasi dalam format teks atau markdown...">{{ $settings['rules']['privacy_policy'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Terms of Service (Syarat &
                            Ketentuan)</label>
                        <textarea name="terms_of_service" rows="10"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 font-mono text-sm"
                            placeholder="Tulis syarat & ketentuan dalam format teks atau markdown...">{{ $settings['rules']['terms_of_service'] ?? '' }}</textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-6 py-3 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 transition-all shadow-md">
                            Simpan Dokumen
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
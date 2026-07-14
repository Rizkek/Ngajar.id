@extends('layouts.app')

@section('title', $campaign->title . ' - Campaign')

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-28">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_0.8fr] items-start">
            <div class="space-y-8">
                <div class="rounded-3xl overflow-hidden bg-gradient-to-br from-brand-100 to-brand-200 p-10 shadow-lg">
                    <div class="flex items-center gap-4 text-brand-700">
                        <span class="material-symbols-rounded text-5xl">volunteer_activism</span>
                        <div>
                            <p class="text-sm uppercase tracking-[0.3em] font-semibold">Campaign</p>
                            <h1 class="mt-2 text-3xl lg:text-4xl font-bold text-slate-900">{{ $campaign->title }}</h1>
                        </div>
                    </div>
                    <p class="mt-6 text-slate-700 leading-relaxed">{{ $campaign->description }}</p>
                </div>

                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-5">
                        <span class="text-sm font-semibold text-slate-600">Target Donasi</span>
                        <span class="text-sm font-semibold text-brand-600">{{ number_format($campaign->progress_percentage, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden mb-4">
                        <div class="bg-brand-600 h-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                        <div>
                            <p class="font-semibold text-slate-900">Terkumpul</p>
                            <p>Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Target</p>
                            <p>Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-8">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Dukung Sekarang</h2>
                    <p class="text-slate-600 mb-6">Donasi kamu langsung masuk ke campaign ini dan membantu kami mencapai target operasional.</p>
                    <a href="{{ route('donasi', ['campaign' => $campaign->slug]) }}"
                        class="block w-full rounded-xl bg-brand-600 text-white py-3 text-center font-semibold hover:bg-brand-700 transition">
                        Donasi ke Campaign
                    </a>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-8">
                    <div class="space-y-3">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Kategori</h3>
                            <p class="text-slate-600">{{ ucfirst($campaign->status) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Slug campaign</h3>
                            <p class="text-slate-600">{{ $campaign->slug }}</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection

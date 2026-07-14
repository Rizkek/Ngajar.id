@extends('layouts.app')

@section('title', 'Campaigns - Ngajar.id')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28">
        <div class="text-center mb-12">
            <p class="text-sm uppercase tracking-[0.3em] text-brand-600 font-semibold">Campaign</p>
            <h1 class="mt-4 text-4xl font-bold text-slate-900">Program Donasi Terukur</h1>
            <p class="mt-4 text-slate-600 max-w-2xl mx-auto">Pilih campaign yang paling sesuai dengan tujuan dukunganmu dan bantu kami memperluas dampak pendidikan untuk para pelajar.</p>
        </div>

        @if($campaigns->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-10 text-center">
                <p class="text-slate-500">Belum ada campaign aktif saat ini. Silakan kembali lagi nanti.</p>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-2">
                @foreach($campaigns as $campaign)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="h-56 bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-brand-700 text-6xl">
                            <span class="material-symbols-rounded">volunteer_activism</span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-xl font-semibold text-slate-900">{{ $campaign->title }}</h2>
                                <span class="text-xs uppercase tracking-[0.24em] font-semibold text-brand-600">{{ ucfirst($campaign->status) }}</span>
                            </div>
                            <p class="text-slate-600 mb-5">{{ \Illuminate\Support\Str::limit($campaign->description, 140) }}</p>
                            <div class="mb-5">
                                <div class="flex justify-between text-sm text-slate-500 mb-2">
                                    <span>Progress</span>
                                    <span>{{ $campaign->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                    <div class="bg-brand-600 h-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('campaigns.show', $campaign->slug) }}"
                                class="inline-flex items-center justify-center w-full rounded-xl bg-brand-600 text-white py-3 text-sm font-semibold hover:bg-brand-700 transition">
                                Dukung Campaign
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection

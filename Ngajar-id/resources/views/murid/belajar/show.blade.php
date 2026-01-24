<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeMateri->judul }} - {{ $kelas->judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Internal:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shrink-0 z-20">
        <div class="flex items-center gap-4">
            <a href="{{ route('murid.kelas') }}" class="text-gray-500 hover:text-teal-600 transition flex items-center gap-2">
                <span class="material-symbols-rounded">arrow_back</span>
                <span class="hidden sm:inline text-sm font-medium">Kembali</span>
            </a>
            <div class="h-6 w-px bg-gray-200"></div>
            <h1 class="text-lg font-bold text-gray-800 line-clamp-1">{{ $kelas->judul }}</h1>
        </div>
        
        <!-- Progress Bar (Simple) -->
        <div class="flex items-center gap-4">
            <div class="hidden md:block w-48">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress Belajar</span>
                    <span class="font-bold text-teal-600">{{ $progress }}%</span>
                </div>
                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-teal-500 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <!-- Profile Avatar (Placeholder) -->
            <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold border border-teal-200">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-80 bg-white border-r border-gray-200 flex flex-col shrink-0 hidden lg:flex">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Daftar Materi</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @foreach($materiList as $index => $materi)
                    @php 
                        $isActive = $materi->materi_id == $activeMateri->materi_id;
                        $isCompleted = false; // Nanti bisa ambil dari DB progress
                    @endphp
                    <a href="{{ route('belajar.show', ['kelas_id' => $kelas->kelas_id, 'materi_id' => $materi->materi_id]) }}" 
                       class="flex items-start gap-3 p-3 rounded-lg transition-colors {{ $isActive ? 'bg-teal-50 border-l-4 border-teal-500' : 'hover:bg-gray-50' }}">
                        <div class="mt-0.5 shrink-0">
                            @if($isActive)
                                <span class="material-symbols-rounded text-teal-600 text-lg">play_circle</span>
                            @elseif($isCompleted)
                                <span class="material-symbols-rounded text-green-500 text-lg">check_circle</span>
                            @else
                                <span class="material-symbols-rounded text-gray-400 text-lg">radio_button_unchecked</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium {{ $isActive ? 'text-teal-800' : 'text-gray-700' }} line-clamp-2">
                                {{ $index + 1 }}. {{ $materi->judul }}
                            </p>
                            <span class="text-xs text-gray-400 flex items-center gap-1 mt-1">
                                <span class="material-symbols-rounded text-[10px]">
                                    {{ $materi->tipe == 'video' ? 'videocam' : ($materi->tipe == 'pdf' ? 'description' : 'article') }}
                                </span>
                                {{ strtoupper($materi->tipe) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col bg-white overflow-hidden relative">
            <!-- Content Scroll Area -->
            <div class="flex-1 overflow-y-auto p-6 md:p-10">
                <div class="max-w-4xl mx-auto">
                    <!-- Title Header -->
                    <div class="mb-8">
                        <span class="text-teal-600 font-semibold text-sm tracking-wide uppercase mb-2 block">Materi Ke-{{ $materiList->search(function($item) use ($activeMateri) { return $item->materi_id == $activeMateri->materi_id; }) + 1 }}</span>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $activeMateri->judul }}</h1>
                        <p class="text-gray-600 text-lg leading-relaxed">{{ $activeMateri->deskripsi }}</p>
                    </div>

                    <!-- Media Content -->
                    <div class="bg-gray-100 rounded-xl overflow-hidden shadow-inner border border-gray-200 aspect-video mb-8 flex items-center justify-center relative">
                        @if($activeMateri->tipe == 'video')
                            <!-- Embed Video (Youtube Support or Direct) -->
                            @if(str_contains($activeMateri->file_url, 'youtube.com') || str_contains($activeMateri->file_url, 'youtu.be'))
                                <iframe class="w-full h-full" src="{{ str_replace('watch?v=', 'embed/', $activeMateri->file_url) }}" frameborder="0" allowfullscreen></iframe>
                            @else
                                <video controls class="w-full h-full bg-black">
                                    <source src="{{ $activeMateri->file_url }}" type="video/mp4">
                                    Browser tidak support video tag.
                                </video>
                            </span>
                        @elseif($activeMateri->tipe == 'pdf')
                            <iframe src="{{ $activeMateri->file_url }}" class="w-full h-full"></iframe>
                        @else
                            <div class="text-center p-8">
                                <span class="material-symbols-rounded text-6xl text-gray-300 mb-4">article</span>
                                <p class="text-gray-500 mb-4">Materi ini berbentuk dokumen/teks.</p>
                                <a href="{{ $activeMateri->file_url }}" target="_blank" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                                    Buka Dokumen
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Text Content (Placeholder) -->
                    <div class="prose prose-lg max-w-none text-gray-700">
                        <p>Silakan pelajari materi di atas dengan seksama.</p>
                        <!-- Nanti bisa tambah field 'content_text' di database untuk artikel panjang -->
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation Bar -->
            <div class="h-20 bg-white border-t border-gray-200 shrink-0 px-6 flex items-center justify-between">
                <div>
                    @if($prevMateri)
                        <a href="{{ route('belajar.show', ['kelas_id' => $kelas->kelas_id, 'materi_id' => $prevMateri->materi_id]) }}" 
                           class="flex items-center gap-2 text-gray-600 hover:text-teal-600 font-medium transition px-4 py-2 rounded-lg hover:bg-gray-50">
                            <span class="material-symbols-rounded">arrow_back</span>
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </a>
                    @else
                        <span class="text-gray-300 flex items-center gap-2 px-4 py-2 cursor-not-allowed">
                            <span class="material-symbols-rounded">arrow_back</span>
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </span>
                    @endif
                </div>

                <div>
                    @if($nextMateri)
                        <a href="{{ route('belajar.show', ['kelas_id' => $kelas->kelas_id, 'materi_id' => $nextMateri->materi_id]) }}" 
                           class="flex items-center gap-2 bg-teal-600 text-white hover:bg-teal-700 font-medium transition px-6 py-3 rounded-lg shadow-md hover:shadow-lg">
                            <span class="hidden sm:inline">Materi Selanjutnya</span>
                            <span class="sm:hidden">Lanjut</span>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </a>
                    @else
                        <button class="flex items-center gap-2 bg-green-500 text-white hover:bg-green-600 font-medium transition px-6 py-3 rounded-lg shadow-md hover:shadow-lg">
                            <span class="material-symbols-rounded">check</span>
                            <span>Selesaikan Kelas</span>
                        </button>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>

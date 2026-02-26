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
    <header class="bg-gray-900 border-b border-gray-800 h-16 flex items-center justify-between px-6 shrink-0 z-20 shadow-md">
        <div class="flex items-center gap-4">
            <a href="{{ route('murid.kelas') }}" class="text-gray-400 hover:text-teal-400 transition flex items-center gap-2 group">
                <span class="material-symbols-rounded group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span class="hidden sm:inline text-sm font-medium">Dashboard</span>
            </a>
            <div class="h-6 w-px bg-gray-700"></div>
            <h1 class="text-sm md:text-base font-bold text-white line-clamp-1 flex items-center gap-2">
                <span class="material-symbols-rounded text-teal-400">school</span>
                {{ $kelas->judul }}
            </h1>
        </div>
        
        <!-- Progress Bar (Simple) -->
        <div class="flex items-center gap-6">
            <div class="hidden md:block w-48 group">
                <div class="flex justify-between text-xs text-gray-400 mb-1Group">
                    <span>Progres Kelas</span>
                    <span class="font-bold text-teal-400">{{ $progress }}%</span>
                </div>
                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-teal-500 to-teal-400 rounded-full transition-all duration-500 shadow-[0_0_10px_rgba(20,184,166,0.5)]" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <!-- Profile Avatar -->
            @php /** @var \App\Models\User $authUser */ $authUser = Auth::user(); @endphp
            <div class="flex items-center gap-3 pl-4 border-l border-gray-700">
                <div class="text-right hidden lg:block">
                     <p class="text-xs text-gray-400">Halo,</p>
                     <p class="text-sm font-bold text-white leading-none">{{ $authUser->name }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center text-white font-bold border-2 border-gray-800 shadow-lg">
                    {{ substr($authUser->name, 0, 1) }}
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-80 bg-slate-50 border-r border-gray-200 flex flex-col shrink-0 hidden lg:flex">
            <div class="p-5 border-b border-gray-200 bg-white">
                <h3 class="font-black text-slate-800 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-rounded text-lg text-teal-600">toc</span>
                    Daftar Materi
                </h3>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar">
                @foreach($materiList as $index => $materi)
                    @php 
                        $isActive = $materi->materi_id == $activeMateri->materi_id;
                        $isCompleted = false; 
                    @endphp
                    <a href="{{ route('belajar.show', ['kelas_id' => $kelas->kelas_id, 'materi_id' => $materi->materi_id]) }}" 
                       class="flex items-start gap-3 p-3.5 rounded-xl transition-all duration-200 group {{ $isActive ? 'bg-white shadow-md shadow-gray-100 border border-teal-100 ring-1 ring-teal-500' : 'hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-100' }}">
                        <div class="mt-0.5 shrink-0">
                            @if($isActive)
                                <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center">
                                     <span class="material-symbols-rounded text-teal-600 text-sm animate-pulse">play_arrow</span>
                                </div>
                            @elseif($isCompleted)
                                <span class="material-symbols-rounded text-green-500 text-xl">check_circle</span>
                            @else
                                <span class="material-symbols-rounded text-gray-300 text-xl group-hover:text-gray-400">radio_button_unchecked</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $isActive ? 'text-teal-900' : 'text-slate-600' }} line-clamp-2 mb-1">
                                {{ $index + 1 }}. {{ $materi->judul }}
                            </p>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md inline-flex items-center gap-1
                                {{ $isActive ? 'bg-teal-50 text-teal-700' : 'bg-gray-200 text-gray-500' }}">
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
        <main class="flex-1 flex flex-col bg-white overflow-hidden relative" x-data="{ activeTab: 'materi' }">
            
            <!-- Tabs Headers -->
            <div class="px-6 md:px-10 pt-6 border-b border-gray-200 bg-white z-10 shrink-0">
                <div class="flex space-x-8 overflow-x-auto scrollbar-hide">
                    <button @click="activeTab = 'materi'" 
                        :class="activeTab === 'materi' ? 'text-teal-600 border-b-2 border-teal-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium border-b-2 border-transparent'"
                        class="pb-3 text-sm whitespace-nowrap transition-all">
                        Materi Belajar
                    </button>
                    <button @click="activeTab = 'diskusi'" 
                        :class="activeTab === 'diskusi' ? 'text-teal-600 border-b-2 border-teal-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium border-b-2 border-transparent'"
                        class="pb-3 text-sm whitespace-nowrap transition-all flex items-center gap-1">
                        Diskusi Kelas
                        <!-- Badge Count Placeholder -->
                    </button>
                    <button @click="activeTab = 'catatan'" 
                        :class="activeTab === 'catatan' ? 'text-teal-600 border-b-2 border-teal-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium border-b-2 border-transparent'"
                        class="pb-3 text-sm whitespace-nowrap transition-all">
                        Catatan Pribadi
                    </button>
                     <button @click="activeTab = 'ulasan'" 
                        :class="activeTab === 'ulasan' ? 'text-teal-600 border-b-2 border-teal-600 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium border-b-2 border-transparent'"
                        class="pb-3 text-sm whitespace-nowrap transition-all">
                        Ulasan & Rating
                    </button>
                </div>
            </div>

            <!-- Content Scroll Area -->
            <div class="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
                <div class="max-w-4xl mx-auto min-h-full">
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 text-green-700">
                             <span class="material-symbols-rounded">check_circle</span>
                             {{ session('success') }}
                        </div>
                    @endif

                    <!-- === TAB MATERI === -->
                    <div x-show="activeTab === 'materi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <!-- Title Header -->
                        <div class="mb-8">
                            <span class="text-teal-600 font-semibold text-sm tracking-wide uppercase mb-2 block">Materi Ke-{{ $materiList->search(function($item) use ($activeMateri) { return $item->materi_id == $activeMateri->materi_id; }) + 1 }}</span>
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $activeMateri->judul }}</h1>
                            <p class="text-gray-600 text-lg leading-relaxed">{{ $activeMateri->deskripsi }}</p>
                        </div>

                        <!-- Media Content -->
                        <div class="bg-gray-100 rounded-xl overflow-hidden shadow-inner border border-gray-200 aspect-video mb-8 flex items-center justify-center relative group">
                            @if($activeMateri->tipe == 'video')
                                <!-- Embed Video (Youtube Support or Direct) -->
                                @if(str_contains($activeMateri->file_url, 'youtube.com') || str_contains($activeMateri->file_url, 'youtu.be'))
                                    <iframe class="w-full h-full" src="{{ str_replace('watch?v=', 'embed/', $activeMateri->file_url) }}" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <video controls class="w-full h-full bg-black">
                                        <source src="{{ $activeMateri->file_url }}" type="video/mp4">
                                        Browser tidak support video tag.
                                    </video>
                                @endif
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

                        <!-- Additional Text Content -->
                        <div class="prose prose-lg max-w-none text-gray-700">
                             <!-- Using raw HTML if stored in DB, otherwise plain text -->
                             {!! nl2br(e($activeMateri->konten ?? 'Silakan pelajari materi di atas dengan seksama.')) !!}
                        </div>
                    </div>

                    <!-- === TAB DISKUSI === -->
                     <div x-show="activeTab === 'diskusi'" x-cloak>
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Diskusi Kelas</h2>
                            <p class="text-gray-600">Tanyakan sesuatu atau berdiskusi dengan teman sekelas dan pengajar.</p>
                        </div>

                        <!-- Form Diskusi -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
                            <form action="{{ route('belajar.diskusi.store', $kelas->kelas_id) }}" method="POST">
                                @csrf
                                <div class="flex gap-4">
                                    <div class="shrink-0 hidden sm:block">
                                        <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold">
                                            {{ substr($authUser->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input type="hidden" name="materi_id" value="{{ $activeMateri->materi_id }}">
                                        <textarea name="konten" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 bg-gray-50 p-3 text-sm" placeholder="Tulis pertanyaan atau tanggapan Anda..." required></textarea>
                                        <div class="mt-3 flex justify-end">
                                            <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition">
                                                Kirim Diskusi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                         <!-- List Diskusi -->
                        <div class="space-y-6">
                            @forelse($diskusi as $d)
                                <div class="flex gap-4">
                                    <div class="shrink-0">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($d->user->name) }}&background=random" class="w-10 h-10 rounded-full">
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-bold text-gray-900">{{ $d->user->name }}</h4>
                                                <span class="text-xs text-gray-500">{{ $d->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm leading-relaxed">{{ $d->konten }}</p>
                                        </div>
                                        
                                        <!-- Replies would go here (Simplified for MVP) -->
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    <span class="material-symbols-rounded text-gray-400 text-4xl mb-2">forum</span>
                                    <p class="text-gray-500">Belum ada diskusi. Jadilah yang pertama bertanya!</p>
                                </div>
                            @endforelse

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $diskusi->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- === TAB CATATAN === -->
                    <div x-show="activeTab === 'catatan'" x-cloak>
                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-1">Catatan Pribadi</h2>
                                <p class="text-gray-600 text-sm">Catatan Anda untuk materi ini (hanya terlihat oleh Anda).</p>
                            </div>
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-bold">Private</span>
                        </div>

                        <form action="{{ route('belajar.catatan.store', $kelas->kelas_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="materi_id" value="{{ $activeMateri->materi_id }}">
                            <div class="relative">
                                <textarea name="catatan" rows="12" 
                                    class="w-full border-2 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 p-4 text-base leading-relaxed bg-yellow-50/30" 
                                    placeholder="Tulis poin-poin penting materi ini di sini...">{{ $catatan->catatan ?? '' }}</textarea>
                                
                                <div class="absolute bottom-4 right-4">
                                    <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 shadow-lg shadow-teal-500/20 transition hover:-translate-y-0.5">
                                        <span class="material-symbols-rounded text-sm">save</span>
                                        Simpan Catatan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- === TAB ULASAN === -->
                    <div x-show="activeTab === 'ulasan'" x-cloak>
                         <div class="max-w-2xl mx-auto">
                            <div class="text-center mb-10">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">Ulasan Kelas</h2>
                                <p class="text-gray-600">Bagaimana pengalaman belajar Anda di kelas ini?</p>
                            </div>

                            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                                <form action="{{ route('belajar.ulasan.store', $kelas->kelas_id) }}" method="POST">
                                    @csrf
                                    
                                    <!-- Star Rating Input -->
                                    <div class="flex justify-center mb-6" x-data="{ rating: {{ $userReview->rating ?? 0 }}, hoverRating: 0 }">
                                        <input type="hidden" name="rating" :value="rating">
                                        <div class="flex gap-2">
                                            <template x-for="star in 5">
                                                <button type="button" 
                                                    @click="rating = star" 
                                                    @mouseenter="hoverRating = star" 
                                                    @mouseleave="hoverRating = 0"
                                                    class="focus:outline-none transition-transform hover:scale-110">
                                                    <span class="material-symbols-rounded text-4xl" 
                                                        :class="(hoverRating || rating) >= star ? 'text-yellow-400 fill-current' : 'text-gray-300'">
                                                        star
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Ulasan Anda</label>
                                        <textarea name="ulasan" rows="4" class="w-full border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500" placeholder="Ceritakan apa yang Anda suka dari kelas ini...">{{ $userReview->ulasan ?? '' }}</textarea>
                                    </div>

                                    <button type="submit" class="w-full py-3 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700 transition shadow-lg shadow-teal-500/20">
                                        {{ $userReview ? 'Update Ulasan' : 'Kirim Ulasan' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Navigation Bar -->
            <div class="h-20 bg-white border-t border-gray-200 shrink-0 px-6 flex items-center justify-between z-20">
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
                        <button onclick="finishMateri('{{ route('belajar.complete', $activeMateri->materi_id) }}', '{{ route('belajar.show', ['kelas_id' => $kelas->kelas_id, 'materi_id' => $nextMateri->materi_id]) }}')" 
                           class="flex items-center gap-2 bg-teal-600 text-white hover:bg-teal-700 font-medium transition px-6 py-3 rounded-lg shadow-md hover:shadow-lg">
                            <span class="hidden sm:inline">Selesai & Lanjut</span>
                            <span class="sm:hidden">Lanjut</span>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </button>
                    @else
                        <button onclick="finishMateri('{{ route('belajar.complete', $activeMateri->materi_id) }}', '{{ route('murid.kelas') }}?completed=true')" 
                            class="flex items-center gap-2 bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 font-medium transition px-6 py-3 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <span class="material-symbols-rounded">check_circle</span>
                            <span>Selesaikan Kelas</span>
                        </button>
                    @endif
                </div>
            </div>
        </main>

    </div>

    <!-- Script Notification -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module">
        // Import tidak diperlukan karena kita sudah attach ke window.ApiClient di app.js
        // Tapi kita perlu tunggu dom ready atau window loaded jika script app.js di-defer
        
        window.finishMateri = function(urlComplete, urlNext) {
            // Menggunakan ApiClient global
            window.ApiClient.post(urlComplete, {})
            .then(data => {
                // Tampilkan notifikasi XP jika sukses dan ada XP gained
                if (data.xp_gained > 0) {
                    Swal.fire({
                        title: 'Tuntas! +' + data.xp_gained + ' XP',
                        text: 'Semangat belajar terus!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        backdrop: `rgba(0,0,123,0.4)`
                    }).then(() => {
                        window.location.href = urlNext;
                    });
                } else {
                    // Kalau sudah pernah dikerjakan, langsung lanjut
                    window.location.href = urlNext;
                }
            })
            .catch(error => {
                console.error('Error completing materi:', error);
                
                // Jika errornya dari ApiClient (misal unauthorized), sudah dihandle redirect.
                // Tapi untuk UX di sini, kita bisa tampilkan error alert
                if (error.status !== 401) {
                     Swal.fire({
                        title: 'Ups!',
                        text: 'Gagal menyelesaikan materi. Coba lagi ya.',
                        icon: 'error'
                    });
                }
            });
        }
    </script>

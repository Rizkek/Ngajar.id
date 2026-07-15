<!-- Footer / Kaki Halaman -->
@unless(request()->is('login') || request()->is('register') || request()->is('password/*'))
    <footer class="bg-slate-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-2xl font-bold text-white">
                            Ngajar.id
                        </span>
                    </div>
                    <p class="text-slate-400 leading-relaxed mb-6">
                        Platform pendidikan inklusif yang menghubungkan semangat relawan dengan mimpi pelajar Indonesia.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <!-- Facebook -->
                        <a href="https://facebook.com/ngajarid" target="_blank" aria-label="Facebook"
                            class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#1877F2] hover:text-white transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.248h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <!-- X (Twitter) -->
                        <a href="https://twitter.com/ngajarid" target="_blank" aria-label="X (Twitter)"
                            class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-black hover:text-white transition-all duration-300 hover:scale-110">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://instagram.com/ngajarid" target="_blank" aria-label="Instagram"
                            class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:text-white transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.332 3.608 1.308.975.975 1.245 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.332 2.633-1.308 3.608-.975-.975-2.242 1.245-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.332-3.608-1.308-.975-.975-1.245-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.245 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948s.014 3.667.072 4.947c.2 4.337 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072s3.667-.014 4.947-.072c4.358-.201 6.78-2.618 6.98-6.98.058-1.281.072-1.689.072-4.948s-.014-3.667-.072-4.947c-.2-4.358-2.618-6.78-6.98-6.98-1.281-.059-1.689-.073-4.948-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <!-- TikTok -->
                        <a href="https://tiktok.com/@ngajarid" target="_blank" aria-label="TikTok"
                            class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-black hover:text-white transition-all duration-300 hover:scale-110">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.9-.32-1.98-.23-2.81.3-.75.47-1.21 1.25-1.28 2.13-.09 1.07.5 2.12 1.41 2.6 1 .53 2.24.4 3.13-.34.61-.5.95-1.22.99-2.01.03-3.24.03-6.48.01-9.72z" />
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="https://youtube.com/@ngajarid" target="_blank" aria-label="YouTube"
                            class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#FF0000] hover:text-white transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6">Jelajahi</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li><a href="{{ url('/') }}" class="hover:text-brand-500 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('programs') }}" class="hover:text-brand-500 transition-colors">Kelas
                                Belajar</a></li>
                        <li><a href="{{ route('mentors') }}" class="hover:text-brand-500 transition-colors">Cari
                                Pengajar</a></li>
                        <li><a href="{{ url('/donasi') }}" class="hover:text-brand-500 transition-colors">Donasi</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6">Layanan</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li><a href="{{ url('/register?role=murid') }}"
                                class="hover:text-brand-500 transition-colors">Untuk Pelajar</a></li>
                        <li><a href="{{ url('/register?role=pengajar') }}"
                                class="hover:text-brand-500 transition-colors">Untuk Pengajar</a></li>
                        <li><a href="{{ route('tentang-kami') }}" class="hover:text-brand-500 transition-colors">Sekolah
                                Mitra</a></li>
                        <li><a href="{{ route('tentang-kami') }}" class="hover:text-brand-500 transition-colors">Karir
                                Relawan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6">Hubungi Kami</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li class="flex items-start gap-4 p-2 rounded-xl hover:bg-slate-800 transition-colors group">
                            <span
                                class="material-symbols-rounded text-brand-500 text-2xl group-hover:scale-110 transition-transform">mail</span>
                            <a href="mailto:halo@ngajar.id"
                                class="text-slate-300 hover:text-white transition-colors flex flex-col">
                                <span class="text-xs text-slate-500 font-medium">Email Kami</span>
                                <span class="font-bold">halo@ngajar.id</span>
                            </a>
                        </li>
                        <li class="flex items-start gap-4 p-2 rounded-xl hover:bg-slate-800 transition-colors group">
                            <span
                                class="material-symbols-rounded text-brand-500 text-2xl group-hover:scale-110 transition-transform">chat</span>
                            <a href="https://wa.me/6281234567890" target="_blank"
                                class="text-slate-300 hover:text-white transition-colors flex flex-col">
                                <span class="text-xs text-slate-500 font-medium">WhatsApp Admin</span>
                                <span class="font-bold">+62 812-3456-7890</span>
                            </a>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-teal-500 mt-0.5 text-xl">location_on</span>
                            <span>Jl. Pendidikan No. 10,<br>Bandung, Indonesia</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">
                    © {{ date('Y') }} Ngajar.ID. All rights reserved.
                </p>
                <div class="flex gap-6 text-sm text-slate-500">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-brand-500">Privacy Policy</a>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-brand-500">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
@endunless

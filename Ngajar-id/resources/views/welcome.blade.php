<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngajar.id - Platform Belajar Mengajar</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
        <div class="container-custom py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gradient">Ngajar.id</h1>
                <div class="flex items-center gap-4">
                    <a href="/login" class="btn btn-outline">Login</a>
                    <a href="/register" class="btn btn-primary">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 bg-gradient-to-br from-primary-50 via-white to-secondary-50">
        <div class="container-custom">
            <div class="max-w-3xl mx-auto text-center animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold text-gradient mb-6">
                    Platform Belajar Mengajar Terbaik
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Akses ribuan kelas dari pengajar terbaik Indonesia. Belajar kapan saja, di mana saja.
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="/kelas" class="btn btn-primary">
                        Lihat Kelas
                    </a>
                    <a href="/about" class="btn btn-secondary">
                        Tentang Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="card text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">1000+</div>
                    <div class="text-gray-600">Kelas Tersedia</div>
                </div>
                <div class="card text-center">
                    <div class="text-4xl font-bold text-secondary-600 mb-2">5000+</div>
                    <div class="text-gray-600">Siswa Aktif</div>
                </div>
                <div class="card text-center">
                    <div class="text-4xl font-bold text-success-600 mb-2">200+</div>
                    <div class="text-gray-600">Pengajar</div>
                </div>
                <div class="card text-center">
                    <div class="text-4xl font-bold text-primary-600 mb-2">4.8/5</div>
                    <div class="text-gray-600">Rating</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16">
        <div class="container-custom">
            <h2 class="text-3xl font-bold text-center mb-12">Kenapa Memilih Ngajar.id?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card card-hover text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Kelas Berkualitas</h3>
                    <p class="text-gray-600 mb-4">Materi pembelajaran yang terstruktur dan mudah dipahami</p>
                    <span class="badge badge-primary">Popular</span>
                </div>

                <!-- Feature 2 -->
                <div class="card card-hover text-center">
                    <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Harga Terjangkau</h3>
                    <p class="text-gray-600 mb-4">Sistem token yang fleksibel sesuai kebutuhan</p>
                    <span class="badge badge-success">Hemat</span>
                </div>

                <!-- Feature 3 -->
                <div class="card card-hover text-center">
                    <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Belajar Kapan Saja</h3>
                    <p class="text-gray-600 mb-4">Akses 24/7 dari perangkat mana saja</p>
                    <span class="badge badge-warning">Fleksibel</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-secondary-600">
        <div class="container-custom text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Siap Memulai Perjalanan Belajar Anda?
            </h2>
            <p class="text-xl text-white/90 mb-8">
                Bergabunglah dengan ribuan siswa lainnya sekarang!
            </p>
            <a href="/register" class="btn bg-white text-primary-600 hover:bg-gray-100">
                Daftar Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Ngajar.id</h3>
                    <p class="text-gray-400">Platform belajar mengajar terbaik di Indonesia</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Navigasi</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/kelas" class="hover:text-white">Kelas</a></li>
                        <li><a href="/pengajar" class="hover:text-white">Pengajar</a></li>
                        <li><a href="/about" class="hover:text-white">Tentang</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/privacy" class="hover:text-white">Privasi</a></li>
                        <li><a href="/terms" class="hover:text-white">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: info@ngajar.id</li>
                        <li>Telepon: 021-XXXX-XXXX</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; 2026 Ngajar.id. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
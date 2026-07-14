<nav class="mt-6 flex-1 space-y-2 px-4 pb-6">
    <a href="{{ route('admin.dashboard') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">dashboard</span>
        <span class="text-base">Dashboard</span>
    </a>

    <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide">Manajemen User</div>
    <a href="{{ route('admin.pengajar.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/pengajar*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">person_book</span>
        <span class="text-base">Pengajar</span>
    </a>
    <a href="{{ route('admin.murid.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/murid*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">school</span>
        <span class="text-base">Murid</span>
    </a>

    <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide mt-4">Konten & Kurikulum</div>
    <a href="{{ route('admin.courses.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/courses*') || request()->is('admin/kelas*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">class</span>
        <span class="text-base">Kelas</span>
    </a>
    <a href="{{ route('admin.learning-paths.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/learning-paths*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">route</span>
        <span class="text-base">Learning Paths</span>
    </a>
    <a href="{{ route('admin.categories.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/categories*') || request()->is('admin/kategori*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">category</span>
        <span class="text-base">Kategori</span>
    </a>
    <a href="{{ route('admin.lessons.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/lessons*') || request()->is('admin/materi*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">menu_book</span>
        <span class="text-base">Materi</span>
    </a>

    <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide mt-4">Keuangan</div>
    <a href="{{ route('admin.donations.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/donations*') || request()->is('admin/donasi*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">volunteer_activism</span>
        <span class="text-base">Donasi</span>
    </a>
    <a href="{{ route('admin.laporan.revenue') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/laporan/revenue*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">monetization_on</span>
        <span class="text-base">Revenue</span>
    </a>

    <div class="px-2 py-2 text-xs font-bold text-white/60 uppercase tracking-wide mt-4">Sistem</div>
    <a href="{{ route('admin.notifications.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/notifications*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">notifications</span>
        <span class="text-base">Notifikasi</span>
    </a>
    <a href="{{ route('admin.settings.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('admin/settings*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">settings</span>
        <span class="text-base">Pengaturan</span>
    </a>
</nav>

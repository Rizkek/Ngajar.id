<nav class="mt-6 flex-1 space-y-2 px-4 pb-6">
    <a href="{{ url('/student/dashboard') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('student/dashboard') || request()->is('murid/dashboard') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">dashboard</span>
        <span class="text-base">Dashboard</span>
    </a>
    <a href="{{ route('student.kelas') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('student/courses') || request()->is('murid/kelas') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">school</span>
        <span class="text-base">Kelas Saya</span>
    </a>
    <a href="{{ route('student.katalog') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('student/catalog') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">search</span>
        <span class="text-base">Katalog Kelas</span>
    </a>
    <a href="{{ route('student.learning-paths.index') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('murid/learning-paths*') || request()->is('learning-paths*') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">route</span>
        <span class="text-base">Learning Path</span>
    </a>
    <a href="{{ route('student.sertifikat') }}"
        class="flex items-center space-x-4 px-4 py-3 rounded hover:bg-teal-700 transition {{ request()->is('student/certificates') || request()->is('murid/sertifikat') ? 'bg-teal-700' : '' }}">
        <span class="material-symbols-rounded w-6 text-center">workspace_premium</span>
        <span class="text-base">Sertifikat</span>
    </a>
</nav>

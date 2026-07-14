@props([
    'title',
    'image',
    'author' => 'Pengajar',
    'authorImage' => null,
    'price' => null,
    'rating' => null,
    'students' => null,
    'progress' => null,
    'url' => '#'
])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 h-full flex flex-col group hover:-translate-y-1">
    
    <!-- Thumbnail -->
    <a href="{{ $url }}" class="relative h-48 overflow-hidden bg-gray-100 block">
        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        
        @if($price === 0 || strtolower($price) === 'gratis')
            <div class="absolute top-4 right-4 z-10">
                <x-badges.status color="green">Gratis</x-badges.status>
            </div>
        @endif
    </a>
        
        <!-- Content -->
        <div class="p-5 flex flex-col flex-grow">
            <!-- Meta (Rating & Students) -->
            <div class="flex justify-between items-center mb-3">
                @if($rating)
                    <div class="flex items-center gap-1 text-sm text-amber-500 font-bold">
                        <x-icons.material name="star" size="sm" />
                        {{ $rating }}
                    </div>
                @endif
                
                @if($students)
                    <div class="flex items-center gap-1 text-xs text-gray-500 font-medium">
                        <x-icons.material name="group" size="sm" />
                        {{ $students }} Siswa
                    </div>
                @endif
            </div>
            
            <!-- Title -->
            <a href="{{ $url }}">
                <h3 class="font-bold text-gray-900 text-lg mb-2 line-clamp-2 group-hover:text-teal-600 transition-colors">
                    {{ $title }}
                </h3>
            </a>
            
            <!-- Author -->
            <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-50">
                <x-avatar :name="$author" :image="$authorImage" size="sm" color="gray" />
                <span class="text-sm text-gray-600 truncate">{{ $author }}</span>
            </div>
            
            <!-- Footer -->
            <div class="mt-4 pt-4 border-t border-gray-50">
                @if(isset($footer))
                    {{ $footer }}
                @else
                    @if(!is_null($progress))
                        <x-progress-bar :percentage="$progress" color="teal" showLabel="true" />
                    @else
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Harga Kelas</span>
                            <span class="font-bold text-teal-600 text-lg">
                                {{ is_numeric($price) && $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Gratis' }}
                            </span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

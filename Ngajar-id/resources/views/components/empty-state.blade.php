@props([
    'icon' => 'folder_open',
    'title' => 'Belum Ada Data',
    'description' => 'Data yang Anda cari tidak ditemukan atau belum ditambahkan.',
    'actionLabel' => null,
    'actionUrl' => '#',
    'actionIcon' => 'add'
])

<div {{ $attributes->merge(['class' => 'text-center py-16 px-4 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/30']) }}>
    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
        <x-icons.material :name="$icon" size="4xl" class="text-gray-300" />
    </div>
    <h4 class="text-lg font-bold text-gray-800">{{ $title }}</h4>
    <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    
    @if($actionLabel)
        <div class="mt-6">
            <x-buttons.primary :icon="$actionIcon" onclick="window.location.href='{{ $actionUrl }}'">
                {{ $actionLabel }}
            </x-buttons.primary>
        </div>
    @endif
    
    {{ $slot }}
</div>

@props(['type' => 'success', 'message', 'timeout' => 3000])

@php
    $config = [
        'success' => [
            'icon' => 'check_circle',
            'iconColor' => 'text-green-500',
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'textColor' => 'text-green-800'
        ],
        'error' => [
            'icon' => 'error',
            'iconColor' => 'text-red-500',
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'textColor' => 'text-red-800'
        ],
        'warning' => [
            'icon' => 'warning',
            'iconColor' => 'text-amber-500',
            'bg' => 'bg-amber-50',
            'border' => 'border-amber-200',
            'textColor' => 'text-amber-800'
        ],
        'info' => [
            'icon' => 'info',
            'iconColor' => 'text-blue-500',
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'textColor' => 'text-blue-800'
        ],
    ][$type] ?? [
        'icon' => 'info',
        'iconColor' => 'text-gray-500',
        'bg' => 'bg-white',
        'border' => 'border-gray-200',
        'textColor' => 'text-gray-800'
    ];
@endphp

<div x-data="{ show: true }"
     x-init="setTimeout(() => show = false, {{ $timeout }})"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed bottom-4 right-4 z-50 max-w-sm w-full {{ $config['bg'] }} {{ $config['border'] }} border rounded-xl shadow-lg p-4 flex gap-3 items-start"
     role="alert">
     
    <x-icons.material :name="$config['icon']" class="{{ $config['iconColor'] }}" />
    
    <div class="flex-1">
        <p class="text-sm font-bold {{ $config['textColor'] }}">
            {{ $message }}
        </p>
    </div>
    
    <button @click="show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
        <x-icons.material name="close" size="sm" />
    </button>
</div>

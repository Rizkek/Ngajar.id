@props([
    'color' => 'gray',
    'type' => 'button',
    'size' => 'md',
    'fullWidth' => false,
    'icon' => null,
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ][$size] ?? 'px-4 py-2 text-sm';

    $widthClass = $fullWidth ? 'w-full' : '';

    $colorClasses = [
        'teal' => 'bg-teal-50 text-teal-700 hover:bg-teal-100 border-teal-200 focus:ring-teal-500',
        'amber' => 'bg-amber-50 text-amber-700 hover:bg-amber-100 border-amber-200 focus:ring-amber-500',
        'gray' => 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300 focus:ring-teal-500 shadow-sm',
        'red' => 'bg-red-50 text-red-700 hover:bg-red-100 border-red-200 focus:ring-red-500',
        'blue' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200 focus:ring-blue-500',
    ][$color] ?? 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300 focus:ring-teal-500 shadow-sm';
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => "inline-flex items-center justify-center border font-bold rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed {$sizeClasses} {$colorClasses} {$widthClass}"]) }}>
        @if($icon)
            <x-icons.material :name="$icon" size="sm" class="mr-2 -ml-1" />
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center border font-bold rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed {$sizeClasses} {$colorClasses} {$widthClass}"]) }}>
        
        @if($icon)
            <x-icons.material :name="$icon" size="sm" class="mr-2 -ml-1" />
        @endif

        {{ $slot }}
    </button>
@endif

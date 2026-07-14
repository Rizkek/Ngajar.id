@props([
    'color' => 'teal',
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
        'teal' => 'bg-teal-600 text-white hover:bg-teal-700 focus:ring-teal-500',
        'indigo' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'red' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'amber' => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-400',
        'gray' => 'bg-gray-800 text-white hover:bg-gray-900 focus:ring-gray-800',
    ][$color] ?? 'bg-teal-600 text-white hover:bg-teal-700 focus:ring-teal-500';
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => "inline-flex items-center justify-center border border-transparent font-bold rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed {$sizeClasses} {$colorClasses} {$widthClass}"]) }}>
        @if($icon)
            <x-icons.material :name="$icon" size="sm" class="mr-2 -ml-1" />
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center border border-transparent font-bold rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed {$sizeClasses} {$colorClasses} {$widthClass}"]) }}>
        
        @if($icon)
            <x-icons.material :name="$icon" size="sm" class="mr-2 -ml-1" />
        @endif

        {{ $slot }}
    </button>
@endif

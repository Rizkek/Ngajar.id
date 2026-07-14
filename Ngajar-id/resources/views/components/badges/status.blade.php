@props([
    'color' => 'gray',
    'dot' => false
])

@php
    $colorClasses = [
        'green' => 'bg-green-100 text-green-800 border-green-200',
        'red' => 'bg-red-100 text-red-800 border-red-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
        'teal' => 'bg-teal-100 text-teal-800 border-teal-200',
        'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
    ][$color] ?? 'bg-gray-100 text-gray-800 border-gray-200';

    $dotColor = [
        'green' => 'bg-green-500',
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'blue' => 'bg-blue-500',
        'teal' => 'bg-teal-500',
        'gray' => 'bg-gray-500',
    ][$color] ?? 'bg-gray-500';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {$colorClasses}"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1.5"></span>
    @endif
    {{ $slot }}
</span>

@props([
    'name' => 'User',
    'image' => null,
    'size' => 'md',
    'color' => 'teal'
])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-14 h-14 text-xl',
        'xl' => 'w-20 h-20 text-3xl',
    ][$size] ?? 'w-10 h-10 text-sm';

    $colorClasses = [
        'teal' => 'bg-teal-100 text-teal-700 border-teal-200',
        'indigo' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'amber' => 'bg-amber-100 text-amber-700 border-amber-200',
        'gray' => 'bg-gray-100 text-gray-700 border-gray-200',
    ][$color] ?? 'bg-teal-100 text-teal-700 border-teal-200';
    
    $initial = strtoupper(substr($name, 0, 1));
@endphp

<div {{ $attributes->merge(['class' => "relative flex-shrink-0 flex items-center justify-center rounded-full font-bold overflow-hidden border {$sizeClasses} {$colorClasses}"]) }}>
    @if($image)
        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover">
    @else
        {{ $initial }}
    @endif
</div>

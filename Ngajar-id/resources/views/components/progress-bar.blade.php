@props([
    'percentage' => 0,
    'color' => 'teal',
    'height' => 'h-2',
    'showLabel' => false,
    'textColor' => 'text-gray-500',
    'wrapperColor' => null,
    'glow' => false
])

@php
    $percentage = max(0, min(100, $percentage));
    
    $bgClasses = [
        'teal' => 'bg-teal-500',
        'indigo' => 'bg-indigo-500',
        'yellow' => 'bg-amber-400',
        'green' => 'bg-green-500',
        'blue' => 'bg-blue-500',
    ][$color] ?? 'bg-teal-500';
    
    $wrapperBg = $wrapperColor ?? [
        'teal' => 'bg-teal-100',
        'indigo' => 'bg-indigo-100',
        'yellow' => 'bg-amber-100',
        'green' => 'bg-green-100',
        'blue' => 'bg-blue-100',
    ][$color] ?? 'bg-gray-100';

    $glowClass = $glow ? "shadow-[0_0_10px_rgba(20,184,166,0.5)]" : "";
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($showLabel)
        <div class="flex justify-between text-xs mb-1 font-bold">
            <span class="{{ $textColor }}">Progress</span>
            <span class="text-{{ $color == 'yellow' ? 'amber' : $color }}-{{ $textColor == 'text-gray-400' ? '400' : '600' }}">{{ $percentage }}%</span>
        </div>
    @endif
    
    <div class="w-full {{ $wrapperBg }} rounded-full {{ $height }} overflow-hidden">
        <div class="{{ $bgClasses }} {{ $height }} rounded-full transition-all duration-1000 ease-out {{ $glowClass }}" 
             style="width: {{ $percentage }}%">
        </div>
    </div>
</div>

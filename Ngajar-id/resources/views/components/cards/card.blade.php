@props([
    'padding' => 'p-6',
    'noShadow' => false,
    'borderColor' => 'border-gray-100',
    'overflow' => 'overflow-hidden'
])

@php
    $shadowClass = $noShadow ? '' : 'shadow-sm';
@endphp

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl border {$borderColor} {$shadowClass} {$overflow}"]) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            {{ $header }}
        </div>
    @endif
    
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
            {{ $footer }}
        </div>
    @endif
</div>

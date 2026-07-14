@props(['name', 'type' => 'rounded', 'size' => 'base'])

@php
    $sizeClasses = [
        'sm' => 'text-sm',
        'base' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
        '2xl' => 'text-2xl',
        '3xl' => 'text-3xl',
        '4xl' => 'text-4xl',
        '5xl' => 'text-5xl',
    ][$size] ?? 'text-base';
@endphp

<span {{ $attributes->merge(['class' => "material-symbols-{$type} {$sizeClasses}"]) }}>
    {{ $name ?? $slot }}
</span>

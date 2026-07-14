@props([
    'disabled' => false,
    'label' => null,
    'id' => null,
    'type' => 'text'
])

@php
    $id = $id ?? $attributes->get('name');
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    @endif
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-colors bg-white disabled:bg-gray-100 disabled:text-gray-500', 'type' => $type, 'id' => $id]) !!}>
</div>

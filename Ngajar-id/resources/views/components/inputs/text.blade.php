@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'icon' => null,
])

<div class="space-y-1 w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-bold text-gray-700">
            {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    
    <div class="relative rounded-xl shadow-sm">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-icons.material :name="$icon" size="sm" class="text-gray-400" />
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'block w-full rounded-xl border-gray-300 focus:ring-teal-500 focus:border-teal-500 sm:text-sm ' . ($icon ? 'pl-10' : 'pl-4')]) }}
        >
    </div>
    
    @error($name)
        <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
            <x-icons.material name="error" size="sm" /> {{ $message }}
        </p>
    @enderror
</div>

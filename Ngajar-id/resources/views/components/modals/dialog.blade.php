@props([
    'name',
    'title',
    'maxWidth' => 'sm:max-w-lg',
    'icon' => 'warning',
    'iconColor' => 'text-amber-500',
    'iconBg' => 'bg-amber-50',
])

<div x-data="{ show: false }"
     x-show="show"
     x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
     x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
     x-on:keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
         @click="show = false"></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full {{ $maxWidth }}">
            
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <!-- Icon -->
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full {{ $iconBg }} sm:mx-0 sm:h-10 sm:w-10">
                        <x-icons.material :name="$icon" class="{{ $iconColor }}" />
                    </div>
                    
                    <!-- Content -->
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">
                            {{ $title }}
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Actions -->
            @if(isset($footer))
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2 border-t border-gray-100">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

@props([
    'label' => '',
    'value' => null,
    'isMoney' => false,
    'withIcon' => false,
    'tooltip' => null,
    'isDay' => false,
])



<div class="text-sm font-medium whitespace-nowrap">
    {{ empty($label) ? '' : $label . ":" }}
</div>

<div class="inline-flex items-center gap-2 relative">

    <x-filament::badge 
        color="{{ !empty($value) ? 'primary' : 'danger' }}" 
        class="w-auto" 
        title="{{ $value }}"
    >
        @if ($isMoney && is_numeric($value))
            ₱ {{ number_format($value, 2) }}
        @elseif (!empty($value) && \Carbon\Carbon::hasFormat($value, 'Y-m-d H:i:s'))
            {{ \Carbon\Carbon::parse($value)->format('F d, Y | g:i A') }}
        @elseif (!empty($value) && \Carbon\Carbon::hasFormat($value, 'Y-m-d'))
            {{ \Carbon\Carbon::parse($value)->format('F d, Y') }}
        @else
            @if ($isDay)
                {{ $value = number_format($value, 0) . ' days' }}
            @else
                {{ $value ?? 'no data' }}
            @endif
        @endif
    </x-filament::badge>

    @if ($tooltip)
        <div x-data="{ open: false }">
            <button 
                @click="open = true"
                class="text-white bg-primary-600 hover:bg-primary-700 px-2 py-1 rounded text-xs ml-2"
                type="button"
            >
                View
            </button>
            <div 
                x-show="open" 
                x-transition 
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            >
                <div 
                    @click.away="open = false"
                    class="bg-white dark:bg-gray-900 text-gray-800 dark:text-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6 space-y-4"
                >
                    <div class="flex justify-between items-center border-b pb-2">
                        <h2 class="text-lg font-semibold">Remarks</h2>
                        <button @click="open = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-white text-lg">
                            &times;
                        </button>
                    </div>

                    <div class="text-sm">
                        {{ $tooltip }}
                    </div>

                    <div class="text-right pt-2">
                        <button 
                            @click="open = false" 
                            class="text-white bg-primary-600 hover:bg-primary-700 px-4 py-1.5 rounded text-sm"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($withIcon)
        @if (!empty($value))
            <x-heroicon-o-check-circle 
                class="w-5 h-5" 
                style="stroke: #1afc66ff !important;" 
            />
        @else
            <x-heroicon-o-x-circle 
                class="w-5 h-5" 
                style="stroke: #ff3232ff !important;" 
            />
        @endif
    @endif
</div>

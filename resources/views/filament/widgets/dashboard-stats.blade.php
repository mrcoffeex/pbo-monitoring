<x-filament::section>
    <div class="flex flex-col gap-3" wire:key="dashboard-stats-{{ $this->year }}">
        <div class="flex items-center justify-end">
            <div class="flex items-center gap-2">
                <label for="year-select" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Year (Current: {{ $this->year }})
                </label>

                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="year" id="year-select">
                        @foreach ($this->getAvailableYears() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <div wire:loading wire:target="year" class="text-primary-600">
                    <x-filament::loading-indicator class="h-4 w-4 text-primary-600" />
                </div>
            </div>
        </div>

        <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-4">
            @if($this->stats)
            @foreach ($this->stats as $stat)
            <div wire:key="stat-{{ $loop->index }}-{{ $this->year }}">
                {{ $stat }}
            </div>
            @endforeach
            @else
            <div class="col-span-full text-center text-gray-500">
                No data available for {{ $this->year }}
            </div>
            @endif
        </div>
    </div>
</x-filament::section>

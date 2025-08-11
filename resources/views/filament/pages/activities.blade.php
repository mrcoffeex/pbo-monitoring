
<x-filament-panels::page>
    <div class="space-y-8">
        <form method="GET" class="mb-8">
            <div 
                class="flex flex-col md:flex-row items-center gap-4 rounded-xl p-4 shadow-sm mb-4 mx-auto bg-white dark:bg-gray-900 p-4 whitespace-nowrap">
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="flex flex-col w-full">
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Start Date</label>
                        <x-filament::input
                            type="date"
                            name="start_date"
                            id="start_date"
                            :value="request('start_date')"
                            class="rounded"
                            style="border: 1px solid #616161ff;" 
                            autofocus
                            required=""
                        />
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="flex flex-col w-full">
                        <label for="end_date" class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">End Date</label>
                        <x-filament::input
                            type="date"
                            name="end_date"
                            id="end_date"
                            :value="request('end_date')"
                            class="rounded"
                            style="border: 1px solid #616161ff;" 
                            required=""
                        />
                    </div>
                </div>
                <div class="flex flex-col w-full md:w-auto justify-end mt-6">
                    <div class="flex gap-2">
                        <x-filament::button type="submit" color="primary" class="flex items-center gap-2">
                            Search Log
                        </x-filament::button>
                        <a href="{{ route(request()->route()->getName()) }}" class="filament-button flex items-center gap-2 bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
        @php
            $start = request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->startOfDay() : now()->subDays(6)->startOfDay();
            $end = request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->endOfDay() : now()->endOfDay();
            $user = auth()->user();
            if ($user && !$user->hasRole('super_admin')) {
                $filtered = collect($activities)->filter(fn($activity) => $activity->created_at >= $start && $activity->created_at <= $end && $activity->causer_id == $user->id);
            } else {
                $filtered = collect($activities)->filter(fn($activity) => $activity->created_at >= $start && $activity->created_at <= $end);
            }
            $grouped = $filtered->groupBy(fn($activity) => $activity->causer?->name ?? 'System');
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            @foreach($grouped as $user => $userActivities)
                <details class="rounded-xl shadow bg-white dark:bg-gray-900 p-4 group" open>
                    <summary class="flex items-center mb-4 cursor-pointer select-none">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-gray-700 dark:text-gray-200 font-bold text-lg">
                            <x-heroicon-o-user class="w-5 h-5" />
                        </div>
                        <span class="ml-3 text-md font-semibold text-gray-700 dark:text-gray-200">{{ $user }}</span>
                        <span class="ml-auto px-2 py-1 rounded text-xs font-bold bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">
                            {{ $userActivities->count() }} {{ ($userActivities->count() > 1) ? 'Activities' : 'Activity' }}
                        </span>
                        <span class="ml-2 flex items-center">
                            <svg class="w-5 h-5 transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </summary>
                    <ul class="space-y-3">
                        @foreach($userActivities as $activity)
                            <li class="flex flex-col bg-gray-50 dark:bg-gray-800 rounded-lg px-4 py-3 border-l-4 border-primary-400 dark:border-primary-600">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex flex-col">
                                        <span class="text-base font-medium text-gray-900 dark:text-gray-100">
                                            {{ str_replace('App\\Models\\', '', $activity->subject_type) . " " . $activity->description }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $activity->created_at->diffForHumans() . " | " . $activity->created_at->format('F d, Y g:i A') }}
                                        </span>
                                    </div>
                                    <span class="ml-2 px-2 py-1 rounded text-xs font-medium bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-300">
                                        {{ $activity->id }}
                                    </span>
                                </div>
                                @if($activity->properties && (isset($activity->properties['attributes']) || isset($activity->properties['old']) || isset($activity->properties['new'])))
                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-primary-600 dark:text-primary-300 font-semibold">Activity</summary>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @if(isset($activity->properties['attributes']))
                                                @foreach($activity->properties['attributes'] as $key => $value)
                                                    <span class="inline-block px-2 py-1 bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 text-xs font-semibold">
                                                        {{ $key }}: {{ is_array($value) ? json_encode($value) : ($value !== null && $value !== '' ? $value : '—') }}
                                                    </span>
                                                @endforeach
                                                    @if(isset($activity->properties['attributes']['remarks']))
                                                @endif
                                            @endif
                                            @if(isset($activity->properties['old']))
                                                <div class="w-full mt-2 flex flex-wrap gap-2">
                                                    <span class="font-bold text-xs text-gray-400 dark:text-gray-500 w-full">Old Values:</span>
                                                    @foreach($activity->properties['old'] as $key => $value)
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                                            {{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if(isset($activity->properties['new']))
                                                <div class="w-full mt-2 flex flex-wrap gap-2">
                                                    <span class="font-bold text-xs text-green-700 dark:text-green-400 w-full">New Values:</span>
                                                    @foreach($activity->properties['new'] as $key => $value)
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-400">
                                                            {{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
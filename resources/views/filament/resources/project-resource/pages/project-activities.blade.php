<x-filament-panels::page>
    @php
        $activities = $this->getActivities();
        $grouped = $this->getGroupedActivities();
        $stats = $this->getActivityStats();
        $dateRange = $this->getDateRange();
        $preset = $dateRange['preset'];
        $subjectOptions = $this->getSubjectFilterOptions();
        $causerOptions = $this->getCauserFilterOptions();
        $hasFilters = $this->hasActiveFilters();
        $filterParams = array_filter([
            'subject' => request('subject'),
            'event' => request('event'),
            'causer' => request('causer'),
        ], fn ($v) => $v !== null && $v !== '');
    @endphp

    {{-- Project context --}}
    <div class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400">
                    Project activity
                </p>
                <h2 class="mt-1 truncate text-lg font-semibold text-gray-950 dark:text-white">
                    {{ $project->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $project->code }} · {{ $project->year }}
                    @if ($project->status)
                        <span class="mx-1 text-gray-300 dark:text-gray-600">·</span>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            {{ $project->status }}
                        </span>
                    @endif
                </p>
                <p class="mt-2 max-w-2xl text-xs text-gray-500 dark:text-gray-400">
                    A chronological record of changes to this project and related procurement records (PR, TWG, payments, and more).
                </p>
            </div>
            <div class="grid shrink-0 grid-cols-2 gap-2 sm:grid-cols-4">
                <div class="rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-lg font-semibold text-gray-950 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 px-3 py-2 dark:bg-emerald-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Created</p>
                    <p class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">{{ $stats['created'] }}</p>
                </div>
                <div class="rounded-lg bg-blue-50 px-3 py-2 dark:bg-blue-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">Updated</p>
                    <p class="text-lg font-semibold text-blue-700 dark:text-blue-300">{{ $stats['updated'] }}</p>
                </div>
                <div class="rounded-lg bg-red-50 px-3 py-2 dark:bg-red-500/10">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">Deleted</p>
                    <p class="text-lg font-semibold text-red-700 dark:text-red-300">{{ $stats['deleted'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 space-y-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-gray-950 dark:text-white">Filter timeline</p>
            @if ($activities->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        x-on:click="$dispatch('expand-all')"
                    >
                        <x-filament::icon icon="heroicon-m-arrows-pointing-out" class="h-4 w-4" />
                        Expand all details
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        x-on:click="$dispatch('collapse-all')"
                    >
                        <x-filament::icon icon="heroicon-m-arrows-pointing-in" class="h-4 w-4" />
                        Collapse all
                    </button>
                </div>
            @endif
        </div>

        {{-- Date presets --}}
        <div class="flex flex-wrap gap-2">
            @foreach ([
                'all' => 'All time',
                '7d' => 'Last 7 days',
                '30d' => 'Last 30 days',
            ] as $key => $label)
                <a
                    href="{{ $this->activitiesUrl([...$filterParams, 'preset' => $key]) }}"
                    @class([
                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold transition',
                        $preset === $key
                            ? 'bg-primary-600 text-white shadow-sm dark:bg-primary-500'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700',
                    ])
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ $this->activitiesUrl() }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-5 lg:items-end">
            @if ($preset !== 'all' && $preset !== 'custom')
                <input type="hidden" name="preset" value="{{ $preset }}" />
            @endif

            <div>
                <label for="subject" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Record type</label>
                <select
                    name="subject"
                    id="subject"
                    class="fi-input block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    onchange="this.form.submit()"
                >
                    <option value="">All records</option>
                    @foreach ($subjectOptions as $class => $label)
                        <option value="{{ $class }}" @selected(request('subject') === $class)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="event" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Action</label>
                <select
                    name="event"
                    id="event"
                    class="fi-input block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    onchange="this.form.submit()"
                >
                    <option value="">All actions</option>
                    <option value="created" @selected(request('event') === 'created')>Created</option>
                    <option value="updated" @selected(request('event') === 'updated')>Updated</option>
                    <option value="deleted" @selected(request('event') === 'deleted')>Deleted</option>
                </select>
            </div>

            <div>
                <label for="causer" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Changed by</label>
                <select
                    name="causer"
                    id="causer"
                    class="fi-input block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    onchange="this.form.submit()"
                >
                    <option value="">Anyone</option>
                    <option value="system" @selected(request('causer') === 'system')>System</option>
                    @foreach ($causerOptions as $user)
                        <option value="{{ $user->id }}" @selected((string) request('causer') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">From</label>
                <x-filament::input type="date" name="start_date" id="start_date" :value="request('start_date')" />
            </div>

            <div>
                <label for="end_date" class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">To</label>
                <x-filament::input type="date" name="end_date" id="end_date" :value="request('end_date')" />
            </div>

            <div class="flex flex-wrap gap-2 lg:col-span-5">
                <x-filament::button type="submit" size="sm">Apply dates</x-filament::button>
                @if ($hasFilters)
                    <x-filament::button
                        tag="a"
                        :href="$this->activitiesUrl()"
                        color="gray"
                        size="sm"
                    >
                        Clear all filters
                    </x-filament::button>
                @endif
            </div>
        </form>

        @if ($hasFilters && $activities->isNotEmpty())
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Showing {{ $stats['total'] }} {{ $stats['total'] === 1 ? 'entry' : 'entries' }} matching your filters.
            </p>
        @endif
    </div>

    {{-- Timeline --}}
    @if ($grouped->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-600 dark:bg-gray-900">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6 text-gray-400" />
            </div>
            <p class="mt-4 text-sm font-semibold text-gray-950 dark:text-white">No activity to show</p>
            <p class="mx-auto mt-1 max-w-md text-sm text-gray-500 dark:text-gray-400">
                @if ($hasFilters)
                    Nothing matches these filters. Try a wider date range or clear filters.
                @else
                    Edits, creates, and deletes on this project and its related records will appear here automatically.
                @endif
            </p>
            @if ($hasFilters)
                <div class="mt-4">
                    <x-filament::button tag="a" :href="$this->activitiesUrl()" color="gray" size="sm">
                        Clear filters
                    </x-filament::button>
                </div>
            @endif
        </div>
    @else
        <div class="space-y-8">
            @foreach ($grouped as $dayLabel => $dayActivities)
                <section>
                    <div class="sticky top-0 z-10 mb-4 flex items-center gap-3 bg-gray-50/95 py-2 backdrop-blur-sm dark:bg-gray-950/95">
                        <span class="text-sm font-bold text-gray-950 dark:text-white">{{ $dayLabel }}</span>
                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            {{ $dayActivities->count() }}
                        </span>
                        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                    </div>

                    <ol class="relative space-y-0 border-l-2 border-gray-200 pl-6 dark:border-gray-700 ml-2">
                        @foreach ($dayActivities as $activity)
                            @php
                                $logger = $this->getLogger($activity);
                                $eventMeta = $this->getEventMeta($activity->event);
                                $subjectLabel = $this->getSubjectLabel($activity);
                                $dotColor = match ($eventMeta['color']) {
                                    'emerald' => 'bg-emerald-500 ring-emerald-100 dark:ring-emerald-900',
                                    'blue' => 'bg-blue-500 ring-blue-100 dark:ring-blue-900',
                                    'red' => 'bg-red-500 ring-red-100 dark:ring-red-900',
                                    'amber' => 'bg-amber-500 ring-amber-100 dark:ring-amber-900',
                                    default => 'bg-gray-400 ring-gray-100 dark:ring-gray-800',
                                };
                            @endphp

                            <li class="relative pb-6 last:pb-0">
                                <span
                                    @class([
                                        'absolute -left-[1.65rem] top-4 h-3 w-3 rounded-full ring-4',
                                        $dotColor,
                                    ])
                                    aria-hidden="true"
                                ></span>

                                @if ($logger)
                                    <div class="overflow-hidden rounded-xl bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-gray-800">
                                        <div class="flex flex-wrap items-center gap-2 bg-gray-50 px-4 py-2.5 dark:bg-gray-900">
                                            <span @class([
                                                'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300' => $eventMeta['color'] === 'emerald',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300' => $eventMeta['color'] === 'blue',
                                                'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300' => $eventMeta['color'] === 'red',
                                                'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' => $eventMeta['color'] === 'amber',
                                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $eventMeta['color'] === 'gray',
                                            ])>
                                                <x-filament::icon :icon="$eventMeta['icon']" class="h-3 w-3" />
                                                {{ $eventMeta['label'] }}
                                            </span>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                {{ $subjectLabel }}
                                                @if ($activity->subject_id)
                                                    <span class="text-gray-400 dark:text-gray-500">#{{ $activity->subject_id }}</span>
                                                @endif
                                            </span>
                                            <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                                                {{ $activity->created_at->format('g:i A') }}
                                            </span>
                                        </div>
                                        <div class="project-activity-log-item dark:bg-gray-900">
                                            @include('filament-activity-log::list.item', [
                                                'activity' => $activity,
                                                'logger' => $logger,
                                            ])
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-gray-800">
                                        <div class="flex flex-wrap items-start gap-3">
                                            <span @class([
                                                'inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300' => $eventMeta['color'] === 'emerald',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300' => $eventMeta['color'] === 'blue',
                                                'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300' => $eventMeta['color'] === 'red',
                                                'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' => $eventMeta['color'] === 'amber',
                                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $eventMeta['color'] === 'gray',
                                            ])>
                                                <x-filament::icon :icon="$eventMeta['icon']" class="h-3 w-3" />
                                                {{ $eventMeta['label'] }}
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-950 dark:text-white">
                                                    {{ $subjectLabel }}
                                                    <span class="font-normal text-gray-500 dark:text-gray-400">— {{ $activity->description }}</span>
                                                </p>
                                                <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-filament::icon icon="heroicon-m-user" class="h-3.5 w-3.5" />
                                                        {{ $activity->causer?->name ?? 'System' }}
                                                    </span>
                                                    <span aria-hidden="true">·</span>
                                                    <span>{{ $activity->created_at->format('g:i A') }}</span>
                                                    <span aria-hidden="true">·</span>
                                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                                </p>
                                                @php
                                                    $changes = $activity->properties ?? collect();
                                                    $attributes = (array) ($changes['attributes'] ?? []);
                                                    $old = (array) ($changes['old'] ?? []);
                                                @endphp
                                                @if (! empty($attributes) || ! empty($old))
                                                    <details class="mt-3">
                                                        <summary class="cursor-pointer text-xs font-semibold text-primary-600 dark:text-primary-400">
                                                            View field changes
                                                        </summary>
                                                        <div class="mt-2 space-y-2 rounded-lg bg-gray-50 p-3 text-xs dark:bg-gray-800/80">
                                                            @foreach ($attributes as $key => $value)
                                                                <div class="flex flex-wrap gap-1">
                                                                    <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $key }}:</span>
                                                                    <span class="text-gray-800 dark:text-gray-200">{{ is_array($value) ? json_encode($value) : ($value ?? '—') }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if (! empty($old))
                                                                <p class="pt-1 text-[10px] font-bold uppercase tracking-wide text-gray-400">Previous values</p>
                                                                @foreach ($old as $key => $value)
                                                                    <div class="flex flex-wrap gap-1 text-gray-500 line-through decoration-gray-400/50">
                                                                        <span class="font-semibold">{{ $key }}:</span>
                                                                        <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </details>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endforeach
        </div>

        <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
            End of activity log · {{ $stats['total'] }} {{ $stats['total'] === 1 ? 'entry' : 'entries' }}
        </p>
    @endif

    @push('styles')
        <style>
            .project-activity-log-item > div {
                padding-top: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                background-color: transparent !important;
                box-shadow: none !important;
            }

            .dark .project-activity-log-item > div {
                background-color: transparent !important;
            }
        </style>
    @endpush
</x-filament-panels::page>

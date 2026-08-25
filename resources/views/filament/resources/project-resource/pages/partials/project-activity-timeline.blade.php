@php
    /** @var \App\Filament\Resources\ProjectResource\Pages\ProjectMonitoring $page */
    $groupedActivities = $groupedActivities ?? collect();
    $fullLogUrl = $fullLogUrl ?? '#';
@endphp

<div id="project-activity-drawer" class="space-y-5">
    @if ($groupedActivities->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 px-4 py-10 text-center dark:border-gray-600">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5 text-gray-400" />
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-950 dark:text-white">No activity yet</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Updates on this project will appear here.</p>
        </div>
    @else
        @foreach ($groupedActivities as $dayLabel => $dayActivities)
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $dayLabel }}</p>
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        {{ $dayActivities->count() }}
                    </span>
                    <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                </div>

                <ol class="space-y-3">
                    @foreach ($dayActivities as $activity)
                        @php
                            $eventMeta = $page->getEventMeta($activity->event);
                            $subjectLabel = $page->getSubjectLabel($activity);
                            $summary = $page->activityChangeSummary($activity);
                            $badgeClasses = match ($eventMeta['color']) {
                                'emerald' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300',
                                'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300',
                                'red' => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300',
                                'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            };
                            $dotClasses = match ($eventMeta['color']) {
                                'emerald' => 'bg-emerald-500 ring-emerald-100 dark:ring-emerald-900',
                                'blue' => 'bg-blue-500 ring-blue-100 dark:ring-blue-900',
                                'red' => 'bg-red-500 ring-red-100 dark:ring-red-900',
                                'amber' => 'bg-amber-500 ring-amber-100 dark:ring-amber-900',
                                default => 'bg-gray-400 ring-gray-100 dark:ring-gray-800',
                            };
                        @endphp

                        <li class="group flex gap-3">
                            <div class="relative flex w-5 shrink-0 flex-col items-center">
                                <span class="relative z-10 mt-3 h-2.5 w-2.5 rounded-full ring-4 ring-white dark:ring-gray-900 {{ $dotClasses }}"></span>
                                <span class="w-0.5 flex-1 bg-gray-200 group-last:hidden dark:bg-gray-700"></span>
                            </div>

                            <div class="min-w-0 flex-1 rounded-xl bg-gray-50 p-3 ring-1 ring-gray-950/5 dark:bg-gray-800/70 dark:ring-white/10">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                                        $badgeClasses,
                                    ])>
                                        <x-filament::icon :icon="$eventMeta['icon']" class="h-3 w-3" />
                                        {{ $eventMeta['label'] }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $subjectLabel }}</span>
                                    <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                                        {{ $activity->created_at?->format('g:i A') }}
                                    </span>
                                </div>

                                @if ($summary)
                                    <p class="mt-2 truncate text-sm text-gray-600 dark:text-gray-300">{{ $summary }}</p>
                                @endif

                                <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $activity->causer?->name ?? 'System' }}</span>
                                    <span aria-hidden="true">·</span>
                                    <span>{{ $activity->created_at?->diffForHumans() }}</span>
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endforeach
    @endif

    <a
        href="{{ $fullLogUrl }}"
        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-950 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200"
    >
        Open full activity log
    </a>
</div>

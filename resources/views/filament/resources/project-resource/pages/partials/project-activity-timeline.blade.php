@php
    /** @var \App\Filament\Resources\ProjectResource\Pages\ProjectMonitoring $page */
    $groupedActivities = $groupedActivities ?? collect();
    $fullLogUrl = $fullLogUrl ?? '#';
@endphp

<div id="project-activity-drawer" class="space-y-5">
    @if ($groupedActivities->isEmpty())
        <div class="activity-empty rounded-xl border border-dashed px-4 py-10 text-center">
            <div class="activity-empty-icon mx-auto flex h-10 w-10 items-center justify-center rounded-full">
                <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5" />
            </div>
            <p class="activity-title mt-3 text-sm font-semibold">No activity yet</p>
            <p class="activity-muted mt-1 text-sm">Updates on this project will appear here.</p>
        </div>
    @else
        @foreach ($groupedActivities as $dayLabel => $dayActivities)
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <p class="activity-muted text-xs font-bold uppercase tracking-wide">{{ $dayLabel }}</p>
                    <span class="activity-count rounded-full px-2 py-0.5 text-[10px] font-semibold">
                        {{ $dayActivities->count() }}
                    </span>
                    <div class="activity-rule h-px flex-1"></div>
                </div>

                <ol class="space-y-3">
                    @foreach ($dayActivities as $activity)
                        @php
                            $eventMeta = $page->getEventMeta($activity->event);
                            $subjectLabel = $page->getSubjectLabel($activity);
                            $summary = $page->activityChangeSummary($activity);
                            $badgeClasses = match ($eventMeta['color']) {
                                'emerald' => 'activity-badge-emerald',
                                'blue' => 'activity-badge-blue',
                                'red' => 'activity-badge-red',
                                'amber' => 'activity-badge-amber',
                                default => 'activity-badge-gray',
                            };
                            $dotClasses = match ($eventMeta['color']) {
                                'emerald' => 'bg-emerald-500',
                                'blue' => 'bg-blue-500',
                                'red' => 'bg-red-500',
                                'amber' => 'bg-amber-500',
                                default => 'bg-gray-400',
                            };
                        @endphp

                        <li class="group flex gap-3">
                            <div class="relative flex w-5 shrink-0 flex-col items-center">
                                <span class="activity-dot relative z-10 mt-3 h-2.5 w-2.5 rounded-full {{ $dotClasses }}"></span>
                                <span class="activity-rail w-0.5 flex-1 group-last:hidden"></span>
                            </div>

                            <div class="activity-card min-w-0 flex-1 rounded-xl p-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $badgeClasses }}">
                                        <x-filament::icon :icon="$eventMeta['icon']" class="h-3 w-3" />
                                        {{ $eventMeta['label'] }}
                                    </span>
                                    <span class="activity-title text-sm font-semibold">{{ $subjectLabel }}</span>
                                    <span class="activity-muted ml-auto text-xs">
                                        {{ $activity->created_at?->format('g:i A') }}
                                    </span>
                                </div>

                                @if ($summary)
                                    <p class="activity-summary mt-2 truncate text-sm">{{ $summary }}</p>
                                @endif

                                <p class="activity-muted mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
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

    <a href="{{ $fullLogUrl }}" class="activity-full-log inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold">
        Open full activity log
    </a>
</div>

<style>
    #project-activity-drawer .activity-empty {
        border-color: rgb(209 213 219);
    }

    #project-activity-drawer .activity-empty-icon {
        background: rgb(243 244 246);
        color: rgb(156 163 175);
    }

    #project-activity-drawer .activity-title {
        color: rgb(3 7 18);
    }

    #project-activity-drawer .activity-muted {
        color: rgb(107 114 128);
    }

    #project-activity-drawer .activity-summary {
        color: rgb(55 65 81);
    }

    #project-activity-drawer .activity-count {
        background: rgb(243 244 246);
        color: rgb(75 85 99);
    }

    #project-activity-drawer .activity-rule,
    #project-activity-drawer .activity-rail {
        background: rgb(229 231 235);
    }

    #project-activity-drawer .activity-card {
        background: rgb(249 250 251);
        box-shadow: inset 0 0 0 1px rgb(17 24 39 / 0.05);
    }

    #project-activity-drawer .activity-dot {
        box-shadow: 0 0 0 4px rgb(255 255 255);
    }

    #project-activity-drawer .activity-badge-emerald {
        background: rgb(209 250 229);
        color: rgb(6 95 70);
    }

    #project-activity-drawer .activity-badge-blue {
        background: rgb(219 234 254);
        color: rgb(30 64 175);
    }

    #project-activity-drawer .activity-badge-red {
        background: rgb(254 226 226);
        color: rgb(153 27 27);
    }

    #project-activity-drawer .activity-badge-amber {
        background: rgb(254 243 199);
        color: rgb(146 64 14);
    }

    #project-activity-drawer .activity-badge-gray {
        background: rgb(243 244 246);
        color: rgb(55 65 81);
    }

    #project-activity-drawer .activity-full-log {
        background: rgb(3 7 18);
        color: rgb(255 255 255);
    }

    #project-activity-drawer .activity-full-log:hover {
        background: rgb(31 41 55);
    }

    .dark #project-activity-drawer .activity-empty {
        border-color: rgb(82 82 91);
        background: rgb(24 24 27);
    }

    .dark #project-activity-drawer .activity-empty-icon {
        background: rgb(39 39 42);
        color: rgb(161 161 170);
    }

    .dark #project-activity-drawer .activity-title {
        color: rgb(250 250 250);
    }

    .dark #project-activity-drawer .activity-muted {
        color: rgb(161 161 170);
    }

    .dark #project-activity-drawer .activity-summary {
        color: rgb(212 212 216);
    }

    .dark #project-activity-drawer .activity-count {
        background: rgb(39 39 42);
        color: rgb(212 212 216);
    }

    .dark #project-activity-drawer .activity-rule,
    .dark #project-activity-drawer .activity-rail {
        background: rgb(63 63 70);
    }

    .dark #project-activity-drawer .activity-card {
        background: rgb(39 39 42);
        box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.08);
    }

    .dark #project-activity-drawer .activity-dot {
        box-shadow: 0 0 0 4px rgb(24 24 27);
    }

    .dark #project-activity-drawer .activity-badge-emerald {
        background: rgb(6 78 59 / 0.45);
        color: rgb(167 243 208);
    }

    .dark #project-activity-drawer .activity-badge-blue {
        background: rgb(30 64 175 / 0.4);
        color: rgb(191 219 254);
    }

    .dark #project-activity-drawer .activity-badge-red {
        background: rgb(153 27 27 / 0.4);
        color: rgb(254 202 202);
    }

    .dark #project-activity-drawer .activity-badge-amber {
        background: rgb(146 64 14 / 0.4);
        color: rgb(253 230 138);
    }

    .dark #project-activity-drawer .activity-badge-gray {
        background: rgb(63 63 70);
        color: rgb(228 228 231);
    }

    .dark #project-activity-drawer .activity-full-log {
        background: rgb(244 244 245);
        color: rgb(24 24 27);
    }

    .dark #project-activity-drawer .activity-full-log:hover {
        background: rgb(228 228 231);
    }
</style>

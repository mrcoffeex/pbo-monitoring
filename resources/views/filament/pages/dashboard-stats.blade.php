@php
    $dashboard = $this->getDashboardData();
    $tones = [
        'emerald' => [
            'card' => 'from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40',
            'label' => 'text-emerald-700 dark:text-emerald-300',
            'value' => 'text-emerald-950 dark:text-emerald-50',
            'hint' => 'text-emerald-600 dark:text-emerald-400',
            'iconWrap' => 'bg-emerald-100 dark:bg-emerald-900/50',
            'icon' => 'text-emerald-600 dark:text-emerald-400',
        ],
        'blue' => [
            'card' => 'from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/40',
            'label' => 'text-blue-700 dark:text-blue-300',
            'value' => 'text-blue-950 dark:text-blue-50',
            'hint' => 'text-blue-600 dark:text-blue-400',
            'iconWrap' => 'bg-blue-100 dark:bg-blue-900/50',
            'icon' => 'text-blue-600 dark:text-blue-400',
        ],
        'indigo' => [
            'card' => 'from-indigo-50 to-violet-50 dark:from-indigo-950/40 dark:to-violet-950/40',
            'label' => 'text-indigo-700 dark:text-indigo-300',
            'value' => 'text-indigo-950 dark:text-indigo-50',
            'hint' => 'text-indigo-600 dark:text-indigo-400',
            'iconWrap' => 'bg-indigo-100 dark:bg-indigo-900/50',
            'icon' => 'text-indigo-600 dark:text-indigo-400',
        ],
        'purple' => [
            'card' => 'from-purple-50 to-fuchsia-50 dark:from-purple-950/40 dark:to-fuchsia-950/40',
            'label' => 'text-purple-700 dark:text-purple-300',
            'value' => 'text-purple-950 dark:text-purple-50',
            'hint' => 'text-purple-600 dark:text-purple-400',
            'iconWrap' => 'bg-purple-100 dark:bg-purple-900/50',
            'icon' => 'text-purple-600 dark:text-purple-400',
        ],
        'amber' => [
            'card' => 'from-amber-50 to-orange-50 dark:from-amber-950/40 dark:to-orange-950/40',
            'label' => 'text-amber-700 dark:text-amber-300',
            'value' => 'text-amber-950 dark:text-amber-50',
            'hint' => 'text-amber-600 dark:text-amber-400',
            'iconWrap' => 'bg-amber-100 dark:bg-amber-900/50',
            'icon' => 'text-amber-600 dark:text-amber-400',
        ],
        'fuchsia' => [
            'card' => 'from-fuchsia-50 to-pink-50 dark:from-fuchsia-950/40 dark:to-pink-950/40',
            'label' => 'text-fuchsia-700 dark:text-fuchsia-300',
            'value' => 'text-fuchsia-950 dark:text-fuchsia-50',
            'hint' => 'text-fuchsia-600 dark:text-fuchsia-400',
            'iconWrap' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50',
            'icon' => 'text-fuchsia-600 dark:text-fuchsia-400',
        ],
        'cyan' => [
            'card' => 'from-cyan-50 to-sky-50 dark:from-cyan-950/40 dark:to-sky-950/40',
            'label' => 'text-cyan-700 dark:text-cyan-300',
            'value' => 'text-cyan-950 dark:text-cyan-50',
            'hint' => 'text-cyan-600 dark:text-cyan-400',
            'iconWrap' => 'bg-cyan-100 dark:bg-cyan-900/50',
            'icon' => 'text-cyan-600 dark:text-cyan-400',
        ],
        'green' => [
            'card' => 'from-green-50 to-lime-50 dark:from-green-950/40 dark:to-lime-950/40',
            'label' => 'text-green-700 dark:text-green-300',
            'value' => 'text-green-950 dark:text-green-50',
            'hint' => 'text-green-600 dark:text-green-400',
            'iconWrap' => 'bg-green-100 dark:bg-green-900/50',
            'icon' => 'text-green-600 dark:text-green-400',
        ],
        'rose' => [
            'card' => 'from-rose-50 to-red-50 dark:from-rose-950/40 dark:to-red-950/40',
            'label' => 'text-rose-700 dark:text-rose-300',
            'value' => 'text-rose-950 dark:text-rose-50',
            'hint' => 'text-rose-600 dark:text-rose-400',
            'iconWrap' => 'bg-rose-100 dark:bg-rose-900/50',
            'icon' => 'text-rose-600 dark:text-rose-400',
        ],
    ];
    $icons = [
        'users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'check' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'bank' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
        'document' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        'currency' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'clipboard' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'badge' => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
        'queue' => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z',
    ];
@endphp

<x-filament-panels::page>
    <div class="dashboard-page">
        <form wire:submit="searchProjects" class="dashboard-toolbar">
            <div class="dashboard-toolbar-search">
                <label for="projectSearch" class="sr-only">Search projects</label>
                <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                    <x-filament::input
                        type="search"
                        id="projectSearch"
                        wire:model="projectSearch"
                        maxlength="255"
                        placeholder="Search projects by name, code, or status"
                    />
                </x-filament::input.wrapper>
            </div>
            <x-filament::button type="submit">
                Search
            </x-filament::button>
            <div class="dashboard-toolbar-year">
                <label for="selectedYear" class="sr-only">Dashboard year</label>
                <x-filament::input.wrapper prefix="Year">
                    <x-filament::input.select id="selectedYear" wire:model.live="selectedYear">
                        @foreach($this->getAvailableYears() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </form>

        <div class="dashboard-top-layout">
            <div class="dashboard-top-main">
                <div class="dashboard-top-cards">
                    @foreach ($dashboard['cards'] as $card)
                        @php
                            $tone = $tones[$card['tone']] ?? $tones['emerald'];
                            $hintClass = match ($card['hintTone'] ?? null) {
                                'positive' => 'text-emerald-600 dark:text-emerald-400',
                                'negative' => 'text-red-600 dark:text-red-400',
                                default => $tone['hint'],
                            };
                        @endphp
                        <div class="dashboard-stat-card min-w-0 rounded-lg bg-gradient-to-br p-3 shadow-sm {{ $tone['card'] }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-medium leading-tight {{ $tone['label'] }}">{{ $card['label'] }}</p>
                                    <p class="mt-1 truncate text-lg font-semibold leading-none {{ $tone['value'] }}" title="{{ $card['hint'] }}">
                                        {{ $card['value'] }}
                                    </p>
                                    <p class="mt-1.5 truncate text-[11px] leading-tight {{ $hintClass }}">{{ $card['hint'] }}</p>
                                </div>
                                <div class="shrink-0 rounded-full p-1.5 {{ $tone['iconWrap'] }}">
                                    <svg class="h-4 w-4 {{ $tone['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icons[$card['icon']] ?? $icons['check'] }}" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <section class="dashboard-top-insights rounded-lg bg-white p-2.5 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10">
                <h2 class="text-xs font-semibold text-gray-900 dark:text-white">Insights</h2>
                <ul class="mt-1.5 space-y-1">
                    @foreach ($dashboard['insights'] as $insight)
                        @php
                            $tone = $tones[$insight['tone']] ?? $tones['amber'];
                        @endphp
                        <li class="flex items-baseline justify-between gap-2 rounded-md bg-gradient-to-r px-2 py-1 {{ $tone['card'] }}" title="{{ $insight['detail'] }}">
                            <span class="min-w-0 truncate text-[11px] font-medium {{ $tone['label'] }}">{{ $insight['title'] }}</span>
                            <span class="shrink-0 text-xs font-semibold {{ $tone['value'] }}">{{ $insight['value'] }}</span>
                        </li>
                    @endforeach
                </ul>

                @if (($dashboard['watchlist'] ?? []) !== [])
                    <p class="mt-2 text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Needs attention</p>
                    <ul class="mt-1 divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($dashboard['watchlist'] as $item)
                            <li>
                                <a
                                    href="{{ $item['url'] }}"
                                    wire:navigate
                                    class="flex items-center justify-between gap-2 py-1 text-xs transition hover:opacity-80"
                                    title="{{ $item['reason'] }}"
                                >
                                    <span class="min-w-0 truncate text-gray-800 dark:text-gray-100">{{ $item['name'] }}</span>
                                    <span class="shrink-0 text-[10px] text-primary-600 dark:text-primary-300">Monitor</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <div
            id="dashboard-charts-root"
            class="mt-4"
            wire:ignore
            data-charts='@json($dashboard['charts'])'
        ></div>
    </div>
</x-filament-panels::page>

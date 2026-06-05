<section class="relative overflow-hidden bg-canvas" id="hero">
    <div class="mx-auto max-w-7xl px-4 pb-16 pt-12 sm:px-6 lg:px-8 lg:pb-20 lg:pt-16">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.32px] text-muted">Provincial Budget Office</p>
            <h1 class="mt-3 text-[28px] font-bold leading-[1.43] text-ink sm:text-[32px] uppercase">
                Real-time provincial infrastructure monitoring
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-body">
                Streamline procurement, obligation, implementation, and payment tracking with actionable transparency for every stakeholder.
            </p>
        </div>

        {{-- Hero labels --}}
        <div class="mx-auto mt-10 max-w-4xl">
            <div class="landing-pill grid grid-cols-1 divide-y divide-hairline sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                @php
                    $heroLabels = [
                        ['title' => 'Projects', 'caption' => 'Where', 'desc' => 'Track lifecycle status across all sectors', 'icon' => 'heroicon-o-squares-2x2', 'accent' => 'bg-rausch/10 text-rausch'],
                        ['title' => 'Budget', 'caption' => 'Obligations', 'desc' => 'Monitor releases, allotments, and balances', 'icon' => 'heroicon-o-banknotes', 'accent' => 'bg-luxe/10 text-luxe'],
                        ['title' => 'Payments', 'caption' => 'Disbursements', 'desc' => 'Follow disbursement progress and schedules', 'icon' => 'heroicon-o-credit-card', 'accent' => 'bg-widget-emerald/10 text-widget-emerald'],
                    ];
                @endphp
                @foreach($heroLabels as $label)
                    <div class="flex items-start gap-4 px-6 py-5">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md {{ $label['accent'] }}">
                            <x-dynamic-component :component="$label['icon']" class="h-5 w-5" />
                        </span>
                        <div class="text-left">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">{{ $label['caption'] }}</p>
                            <p class="mt-0.5 text-base font-semibold text-ink">{{ $label['title'] }}</p>
                            <p class="mt-1 text-sm leading-snug text-muted">{{ $label['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Status strip --}}
        <div class="mx-auto mt-8 flex max-w-4xl flex-wrap items-center justify-center gap-6 text-sm font-medium text-muted">
            <span class="flex items-center gap-2 text-ink">
                <span class="h-2 w-2 rounded-full bg-rausch"></span>
                Live data sync
            </span>
            <span class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-luxe"></span>
                Role-based access
            </span>
            <span class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-widget-emerald"></span>
                Secure audit trail
            </span>
        </div>

        {{-- Preview cards grid --}}
        <div class="mt-16">
            <div class="mb-6 text-center">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">Key metrics</p>
                <p class="mt-1 text-base font-semibold text-ink">Platform snapshot</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $previewCards = [
                        [
                            'label' => 'Active projects',
                            'value' => '128',
                            'meta' => 'Across all sectors',
                            'badge' => 'Live',
                            'gradient' => 'from-rausch/25 via-rausch/10 to-canvas',
                            'valueColor' => 'text-rausch',
                            'dot' => 'bg-rausch',
                        ],
                        [
                            'label' => 'Procurements',
                            'value' => '54',
                            'meta' => 'In progress this quarter',
                            'badge' => null,
                            'gradient' => 'from-luxe/25 via-luxe/10 to-canvas',
                            'valueColor' => 'text-luxe',
                            'dot' => 'bg-luxe',
                        ],
                        [
                            'label' => 'Payment rate',
                            'value' => '91%',
                            'meta' => 'On schedule',
                            'badge' => 'On track',
                            'gradient' => 'from-widget-emerald/25 via-widget-emerald/10 to-canvas',
                            'valueColor' => 'text-widget-emerald',
                            'dot' => 'bg-widget-emerald',
                        ],
                        [
                            'label' => 'Pending approvals',
                            'value' => '10',
                            'meta' => 'Requires action',
                            'badge' => null,
                            'gradient' => 'from-widget-amber/25 via-widget-amber/10 to-canvas',
                            'valueColor' => 'text-widget-amber',
                            'dot' => 'bg-widget-amber',
                        ],
                    ];
                @endphp
                @foreach($previewCards as $card)
                    <article class="landing-card group overflow-hidden">
                        <div class="relative aspect-[4/3] bg-gradient-to-br {{ $card['gradient'] }} p-5">
                            @if($card['badge'])
                                <span class="landing-badge absolute left-3 top-3">{{ $card['badge'] }}</span>
                            @endif
                            <span class="absolute right-3 top-3 h-2.5 w-2.5 rounded-full {{ $card['dot'] }}"></span>
                            <div class="flex h-full flex-col justify-end">
                                <p class="text-sm font-medium text-body">{{ $card['label'] }}</p>
                                <p class="mt-1 text-[32px] font-bold leading-none {{ $card['valueColor'] }}">{{ $card['value'] }}</p>
                            </div>
                        </div>
                        <div class="border-t border-hairline px-4 py-3">
                            <p class="text-sm text-muted">{{ $card['meta'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('filament.admin.auth.login') }}" class="btn-rausch">
                Open dashboard
                <x-heroicon-o-arrow-right class="h-5 w-5"/>
            </a>
            <a href="#features" class="btn-secondary">
                Explore features
            </a>
        </div>
    </div>
</section>

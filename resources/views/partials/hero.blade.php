<section class="relative overflow-hidden bg-canvas" id="hero" aria-labelledby="hero-heading">
    <div class="mx-auto max-w-7xl px-4 pb-16 pt-10 sm:px-6 lg:px-8 lg:pb-16 lg:pt-12">
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <p class="text-sm font-medium text-muted">Provincial Budget Office · Davao del Sur</p>
                <h1 id="hero-heading" class="mt-3 text-[28px] font-bold leading-[1.43] text-ink text-balance">
                    Follow every project from budget to payment
                </h1>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-body">
                    Authorized staff can track procurement, obligations, implementation, and disbursements in one official record.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('filament.admin.auth.login') }}" class="btn-rausch">
                        Sign in to the dashboard
                    </a>
                    <a href="#how-it-works" class="btn-secondary">
                        See how it works
                    </a>
                </div>
            </div>

            @php
                $heroOptions = [
                    [
                        'title' => 'Projects',
                        'meta' => 'Find a record and see its current stage.',
                        'icon' => 'heroicon-o-folder-open',
                        'href' => '#projects',
                        'badge' => 'Start here',
                    ],
                    [
                        'title' => 'Budget',
                        'meta' => 'Review allotments, releases, and remaining balances.',
                        'icon' => 'heroicon-o-banknotes',
                        'href' => '#budget',
                        'badge' => null,
                    ],
                    [
                        'title' => 'Payments',
                        'meta' => 'Follow disbursements and amounts still due.',
                        'icon' => 'heroicon-o-currency-dollar',
                        'href' => '#payments',
                        'badge' => null,
                    ],
                ];
            @endphp

            <div>
                <p class="text-sm font-medium text-muted">Choose a starting point</p>
                <ul class="mt-4 grid gap-4">
                    @foreach($heroOptions as $option)
                        <li>
                            <a href="{{ $option['href'] }}" class="property-card group flex items-center gap-4 rounded-md p-1">
                                <div class="property-card-photo !aspect-square h-24 w-24 shrink-0 sm:h-28 sm:w-28">
                                    @if($option['badge'])
                                        <span class="landing-badge absolute left-2 top-2">{{ $option['badge'] }}</span>
                                    @endif
                                    <span class="absolute inset-0 flex items-center justify-center text-ink">
                                        <x-dynamic-component :component="$option['icon']" class="h-8 w-8" aria-hidden="true" />
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 py-1">
                                    <p class="text-base font-semibold text-ink">{{ $option['title'] }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-muted">{{ $option['meta'] }}</p>
                                    <span class="mt-2 inline-flex items-center gap-2 text-sm font-medium text-ink">
                                        View this section
                                        <x-heroicon-o-arrow-right class="h-4 w-4 transition-transform group-hover:translate-x-0.5" aria-hidden="true" />
                                    </span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

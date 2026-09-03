@php
    $heroPhotos = [
        [
            'src' => 'images/hero/kapatagan-mt-apo.jpg',
            'alt' => 'Mount Apo seen from Kapatagan, Digos City, Davao del Sur',
        ],
        [
            'src' => 'images/hero/mt-apo-drone.jpg',
            'alt' => 'Aerial view of Mount Apo from Digos City, Davao del Sur',
        ],
        [
            'src' => 'images/hero/digos-overlooking.jpg',
            'alt' => 'Overlooking Digos City, capital of Davao del Sur',
        ],
        [
            'src' => 'images/hero/passig-islet.jpg',
            'alt' => 'Pasig Islet in Santa Cruz, Davao del Sur',
        ],
        [
            'src' => 'images/hero/digos-poblacion.jpg',
            'alt' => 'Aerial view of Digos Poblacion, Davao del Sur',
        ],
    ];
@endphp

<section class="relative overflow-hidden" id="hero" aria-labelledby="hero-heading" data-hero-carousel>
    <div class="absolute inset-0" aria-hidden="true">
        @foreach($heroPhotos as $index => $photo)
            <img src="{{ asset($photo['src']) }}"
                 alt=""
                 class="hero-photo{{ $index === 0 ? ' is-active' : '' }}"
                 data-hero-slide
                 width="1920"
                 height="1080"
                 @if($index === 0) fetchpriority="high" @else loading="lazy" @endif />
        @endforeach
        <div class="hero-overlay"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 pb-14 pt-10 sm:px-6 lg:px-8 lg:pb-20 lg:pt-16">
        <div class="max-w-3xl">
            <p class="text-sm font-medium text-white/75">Provincial Budget Office · Davao del Sur</p>
            <h1 id="hero-heading" class="mt-3 text-[28px] font-bold leading-[1.3] text-on-dark text-balance sm:text-[40px] sm:leading-[1.2]">
                Follow every project from budget to payment
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/85 sm:text-lg">
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
                    'href' => '#projects',
                ],
                [
                    'title' => 'Budget',
                    'meta' => 'Review allotments, releases, and remaining balances.',
                    'href' => '#budget',
                ],
                [
                    'title' => 'Payments',
                    'meta' => 'Follow disbursements and amounts still due.',
                    'href' => '#payments',
                ],
            ];
        @endphp

        <ul class="mt-12 grid gap-4 sm:grid-cols-3">
            @foreach($heroOptions as $option)
                <li>
                    <a href="{{ $option['href'] }}" class="landing-card group block h-full p-5">
                        <p class="text-base font-semibold text-ink">{{ $option['title'] }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $option['meta'] }}</p>
                        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-ink">
                            View this section
                            <x-heroicon-o-arrow-right class="h-4 w-4 transition-transform group-hover:translate-x-0.5" aria-hidden="true" />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2" role="group" aria-label="Photographs of Davao del Sur">
                @foreach($heroPhotos as $index => $photo)
                    <button type="button"
                            class="hero-dot"
                            data-hero-dot
                            aria-label="{{ $photo['alt'] }}"
                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                @endforeach
            </div>
            <p class="text-[11px] text-white/60">
                Photos of Davao del Sur via
                <a href="https://commons.wikimedia.org/wiki/Category:Davao_del_Sur" class="underline-offset-2 hover:underline" target="_blank" rel="noopener">Wikimedia Commons</a>
                (CC BY-SA)
            </p>
        </div>
    </div>
</section>

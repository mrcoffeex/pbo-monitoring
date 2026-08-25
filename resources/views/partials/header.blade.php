<div class="official-banner">
    <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-2 sm:px-6 lg:px-8">
        <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-ink" aria-hidden="true">
            <span class="h-1.5 w-1.5 rounded-full bg-canvas"></span>
        </span>
        <p>An official website of the Provincial Government of Davao del Sur</p>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-hairline bg-canvas/95 backdrop-blur supports-[backdrop-filter]:bg-canvas/80">
    <div class="relative mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="/" class="inline-flex min-w-0 items-center gap-3">
            <img src="{{ asset('images/dds-logo.png') }}"
                 alt="Province of Davao del Sur official seal"
                 class="h-11 w-11 shrink-0 object-contain"
                 width="44"
                 height="44" />
            <span class="min-w-0">
                <span class="block truncate text-base font-semibold leading-tight text-ink">{{ config('app.name', 'Infra Monitoring') }}</span>
                <span class="hidden truncate text-sm text-muted sm:block">Provincial Budget Office</span>
            </span>
        </a>

        <nav class="absolute left-1/2 top-1/2 hidden -translate-x-1/2 -translate-y-1/2 items-end gap-2 lg:flex" aria-label="Primary">
            <a href="#projects" class="product-tab is-active" data-nav-section="projects">
                <x-heroicon-o-folder-open class="h-8 w-8" aria-hidden="true" />
                <span class="text-sm font-semibold">Projects</span>
            </a>
            <a href="#budget" class="product-tab" data-nav-section="budget">
                <x-heroicon-o-banknotes class="h-8 w-8" aria-hidden="true" />
                <span class="text-sm font-semibold">Budget</span>
            </a>
            <a href="#payments" class="product-tab" data-nav-section="payments">
                <x-heroicon-o-currency-dollar class="h-8 w-8" aria-hidden="true" />
                <span class="text-sm font-semibold">Payments</span>
            </a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="#contact" class="landing-nav-link hidden xl:inline">Contact</a>
            <button id="themeToggle" type="button"
                class="icon-btn-circle"
                aria-label="Switch to dark theme">
                <x-heroicon-o-sun class="h-5 w-5" id="sun"/>
                <x-heroicon-o-moon class="h-5 w-5 hidden" id="moon"/>
            </button>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="hidden sm:inline-flex btn-rausch !h-10 !min-h-0 !px-5 !text-sm">
                Sign in
            </a>
            <button id="menuBtn" type="button"
                class="icon-btn-circle lg:hidden"
                aria-label="Open menu"
                aria-expanded="false"
                aria-controls="mobileNav">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobileNav" class="hidden border-t border-hairline bg-canvas px-4 pb-4 lg:hidden">
        <nav class="flex flex-col gap-1 pt-3" aria-label="Mobile">
            <a href="#projects" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">Projects</a>
            <a href="#budget" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">Budget</a>
            <a href="#payments" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">Payments</a>
            <a href="#how-it-works" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">How it works</a>
            <a href="#features" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">What you can do</a>
            <a href="#team" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">The team</a>
            <a href="#contact" class="rounded-sm px-3 py-2.5 text-base font-medium text-body hover:bg-surface-soft">Contact</a>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="mt-2 inline-flex btn-rausch !h-10 !min-h-0 w-full !text-sm">
                Sign in
            </a>
        </nav>
    </div>
</header>

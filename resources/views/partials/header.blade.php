<div class="official-banner">
    <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-1 sm:px-6 lg:px-8">
        <span class="inline-flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded-full bg-ink" aria-hidden="true">
            <span class="h-1 w-1 rounded-full bg-canvas"></span>
        </span>
        <p>An official website of the Provincial Government of Davao del Sur</p>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-hairline bg-canvas/95 backdrop-blur supports-[backdrop-filter]:bg-canvas/80">
    <div class="mx-auto flex h-14 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="/" class="inline-flex min-w-0 items-center gap-2.5">
            <img src="{{ asset('images/dds-logo.png') }}"
                 alt="Province of Davao del Sur official seal"
                 class="h-8 w-8 shrink-0 object-contain"
                 width="32"
                 height="32" />
            <span class="min-w-0">
                <span class="block truncate text-sm font-semibold leading-tight text-ink">{{ config('app.name', 'Infra Monitoring') }}</span>
                <span class="hidden truncate text-xs text-muted sm:block">Provincial Budget Office</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="Primary">
            <a href="#how-it-works" class="landing-nav-link" data-nav-section="how-it-works">How it works</a>
            <a href="#features" class="landing-nav-link" data-nav-section="features">What you can do</a>
            <a href="#audience" class="landing-nav-link" data-nav-section="audience">Who it's for</a>
            <a href="#faq" class="landing-nav-link" data-nav-section="faq">FAQ</a>
            <a href="#contact" class="landing-nav-link" data-nav-section="contact">Contact</a>
        </nav>

        <div class="flex items-center gap-2">
            <button id="themeToggle" type="button"
                class="icon-btn-circle"
                aria-label="Switch to dark theme">
                <x-heroicon-o-sun class="h-4 w-4" id="sun"/>
                <x-heroicon-o-moon class="h-4 w-4 hidden" id="moon"/>
            </button>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="hidden sm:inline-flex btn-rausch !h-8 !min-h-0 !px-4 !text-sm">
                Sign in
            </a>
            <button id="menuBtn" type="button"
                class="icon-btn-circle lg:hidden"
                aria-label="Open menu"
                aria-expanded="false"
                aria-controls="mobileNav">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobileNav" class="hidden border-t border-hairline bg-canvas px-4 pb-3 lg:hidden">
        <nav class="flex flex-col gap-0.5 pt-2" aria-label="Mobile">
            <a href="#how-it-works" class="rounded-sm px-3 py-2 text-sm font-medium text-body hover:bg-surface-soft">How it works</a>
            <a href="#features" class="rounded-sm px-3 py-2 text-sm font-medium text-body hover:bg-surface-soft">What you can do</a>
            <a href="#audience" class="rounded-sm px-3 py-2 text-sm font-medium text-body hover:bg-surface-soft">Who it's for</a>
            <a href="#faq" class="rounded-sm px-3 py-2 text-sm font-medium text-body hover:bg-surface-soft">FAQ</a>
            <a href="#team" class="rounded-sm px-3 py-2 text-sm font-medium text-body hover:bg-surface-soft">The team</a>
            <a href="#contact" class="rounded-sm px-3 py-2 text-sm font-medium text-body hover:bg-surface-soft">Contact</a>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="mt-2 inline-flex btn-rausch !h-9 !min-h-0 w-full !text-sm">
                Sign in
            </a>
        </nav>
    </div>
</header>

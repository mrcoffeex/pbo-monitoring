<header class="sticky top-0 z-40 border-b border-hairline bg-canvas/95 backdrop-blur supports-[backdrop-filter]:bg-canvas/80 transition-colors">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="/" class="inline-flex items-center gap-3">
            <img src="{{ asset('images/dds-logo.png') }}"
                 alt="Province of Davao del Sur official seal"
                 class="h-11 w-11 shrink-0 object-contain"
                 width="44"
                 height="44" />
            <span class="text-base font-semibold tracking-tight text-ink">{{ config('app.name', 'Laravel') }}</span>
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            <a href="#features" class="landing-nav-link">Features</a>
            <a href="#dev-team" class="landing-nav-link">Dev Team</a>
            <a href="#contact" class="landing-nav-link">Contact</a>
        </nav>

        <div class="flex items-center gap-3">
            <button id="themeToggle" type="button"
                class="icon-btn-circle"
                aria-label="Toggle Theme">
                <x-heroicon-o-sun class="h-5 w-5" id="sun"/>
                <x-heroicon-o-moon class="h-5 w-5 hidden" id="moon"/>
            </button>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="hidden sm:inline-flex btn-secondary !h-10 !min-h-0 !px-5 !text-sm">
                Login
            </a>
            <button id="menuBtn" type="button"
                class="icon-btn-circle md:hidden"
                aria-label="Open menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobileNav" class="hidden border-t border-hairline bg-canvas px-4 pb-4 md:hidden">
        <nav class="flex flex-col gap-1 pt-3">
            <a href="#features" class="rounded-sm px-3 py-2.5 text-sm font-medium text-body hover:bg-surface-soft">Features</a>
            <a href="#dev-team" class="rounded-sm px-3 py-2.5 text-sm font-medium text-body hover:bg-surface-soft">Dev Team</a>
            <a href="#contact" class="rounded-sm px-3 py-2.5 text-sm font-medium text-body hover:bg-surface-soft">Contact</a>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="mt-2 inline-flex btn-rausch !h-10 !min-h-0 w-full !text-sm">
                Login
            </a>
        </nav>
    </div>
</header>

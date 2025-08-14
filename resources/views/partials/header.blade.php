<header class="sticky top-0 z-40 border-b border-gray-200 dark:border-gray-800 backdrop-blur bg-white/80 dark:bg-gray-900/70 supports-[backdrop-filter]:bg-white/60 supports-[backdrop-filter]:dark:bg-gray-900/60 transition-[background-color,backdrop-filter,border-color]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/" class="inline-flex items-center gap-2 font-extrabold text-lg tracking-tight">
            <span class="bg-gradient-to-r from-pink-600 via-fuchsia-500 to-gray-600 dark:from-pink-400 dark:via-fuchsia-400 dark:to-gray-400 bg-clip-text text-transparent font-google-code">{{ config('app.name', 'Laravel') }}</span>
        </a>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium font-google-code">
            <a href="#features" class="hover:text-pink-600 dark:hover:text-pink-400 text-gray-900 dark:text-gray-300 transition-colors">Features</a>
            <a href="#dev-team" class="hover:text-pink-600 dark:hover:text-pink-400 text-gray-900 dark:text-gray-300 transition-colors">Dev Team</a>
            <a href="#contact" class="hover:text-pink-600 dark:hover:text-pink-400 text-gray-900 dark:text-gray-300 transition-colors">Contact</a>
        </nav>
        <div class="flex items-center gap-3">
            <button id="themeToggle" type="button"
                class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                aria-label="Toggle Theme">
                <x-heroicon-o-sun class="h-5 w-5" id="sun"/>
                <x-heroicon-o-moon class="h-5 w-5" id="moon"/>
            </button>
            <a href="{{ route('filament.admin.auth.login') }}"
               class="inline-flex items-center gap-1 rounded-lg border border-pink-600 dark:border-pink-500 text-pink-600 dark:text-pink-400 hover:bg-pink-50 dark:hover:bg-pink-900/30 hover:text-pink-700 dark:hover:text-pink-300 px-4 h-9 text-sm font-medium transition-colors">
                Login
            </a>
            <button id="menuBtn" class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <span class="sr-only">Menu</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
    <div id="mobileNav" class="md:hidden hidden border-t border-gray-200 dark:border-gray-800 px-4 pb-4 bg-white dark:bg-gray-900">
        <nav class="flex flex-col gap-2 pt-3 text-sm font-medium">
            <a href="#features" class="px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300">Features</a>
            <a href="#dev-team" class="px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300">Dev Team</a>
            <a href="#contact" class="px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300">Contact</a>
        </nav>
    </div>
</header>
<script>
    // Optional: elevate header background after scroll
    const hdr = document.currentScript.previousElementSibling;
    const solidClass = 'bg-white/95 dark:bg-gray-900/85 shadow-sm';
    let applied = false;
    window.addEventListener('scroll', () => {
        const should = window.scrollY > 8;
        if (should && !applied) { hdr.classList.add(...solidClass.split(' ')); applied = true; }
        else if (!should && applied) { hdr.classList.remove(...solidClass.split(' ')); applied = false; }
    });
</script>

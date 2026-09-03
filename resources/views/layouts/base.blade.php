<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Infra Monitoring') }} | Provincial Budget Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Official infrastructure monitoring system of the Provincial Budget Office, Davao del Sur. Track projects, budget obligations, and payments in one place." />
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script>
        (function () {
            const t = localStorage.getItem('theme');
            const prefers = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (t === 'dark' || (!t && prefers)) document.documentElement.classList.add('dark');
        })();
    </script>
    @stack('head')
</head>
<body class="landing-page min-h-full">
    <a href="#main-content" class="skip-link">Skip to main content</a>

    @include('partials.header')

    <main id="main-content" class="relative">
        @yield('hero')
        @yield('how-it-works')
        @yield('features')
        @yield('audience')
        @yield('faq')
        @yield('dev-team')
        @yield('contact')
    </main>

    @include('partials.footer')

    <script>
        function syncThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.getElementById('sun')?.classList.toggle('hidden', isDark);
            document.getElementById('moon')?.classList.toggle('hidden', !isDark);
            const toggle = document.getElementById('themeToggle');
            if (toggle) {
                toggle.setAttribute('aria-label', isDark ? 'Switch to light theme' : 'Switch to dark theme');
            }
        }
        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const root = document.documentElement;
            const now = root.classList.toggle('dark');
            localStorage.setItem('theme', now ? 'dark' : 'light');
            syncThemeIcons();
        });
        syncThemeIcons();

        const menuBtn = document.getElementById('menuBtn');
        const mobileNav = document.getElementById('mobileNav');
        menuBtn?.addEventListener('click', () => {
            const isOpen = ! mobileNav?.classList.toggle('hidden');
            menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            menuBtn.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
        });

        document.querySelectorAll('a[href^="#"]').forEach((link) => {
            link.addEventListener('click', (event) => {
                const href = link.getAttribute('href');
                if (!href || href === '#' || !href.startsWith('#')) {
                    return;
                }
                const target = document.querySelector(href);
                if (!target) {
                    return;
                }
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (mobileNav && !mobileNav.classList.contains('hidden')) {
                    mobileNav.classList.add('hidden');
                    menuBtn?.setAttribute('aria-expanded', 'false');
                    menuBtn?.setAttribute('aria-label', 'Open menu');
                }
            });
        });

        const navTabs = Array.from(document.querySelectorAll('[data-nav-section]'));
        const navTargets = navTabs
            .map((tab) => document.getElementById(tab.dataset.navSection))
            .filter(Boolean);

        if (navTabs.length && navTargets.length && 'IntersectionObserver' in window) {
            const setActiveTab = (id) => {
                navTabs.forEach((tab) => {
                    tab.classList.toggle('is-active', tab.dataset.navSection === id);
                });
            };

            const observer = new IntersectionObserver((entries) => {
                const visible = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

                if (visible[0]?.target?.id) {
                    setActiveTab(visible[0].target.id);
                }
            }, {
                rootMargin: '-30% 0px -50% 0px',
                threshold: [0.2, 0.5, 1],
            });

            navTargets.forEach((section) => observer.observe(section));
        }
    </script>
    @stack('scripts')
</body>
</html>

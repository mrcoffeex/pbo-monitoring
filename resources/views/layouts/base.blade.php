<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'PBO Monitoring') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
<body class="min-h-full antialiased bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-200 font-sans transition-colors duration-300">
    @include('partials.header')

    <main id="pageContainer" class="relative opacity-0 translate-y-4 transition-all duration-700 ease-out">
        @yield('hero')
        @yield('features')
        @yield('dev-team')
        @yield('contact')
    </main>

    @include('partials.footer')

    <script>
        function syncThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.getElementById('sun')?.classList.toggle('hidden', isDark);
            document.getElementById('moon')?.classList.toggle('hidden', !isDark);
        }
        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const root = document.documentElement;
            const now = root.classList.toggle('dark');
            localStorage.setItem('theme', now ? 'dark' : 'light');
            syncThemeIcons();
        });
        syncThemeIcons();
        document.getElementById('menuBtn')?.addEventListener('click', () => {
            document.getElementById('mobileNav')?.classList.toggle('hidden');
        });
        // Initial page entrance animation
        window.addEventListener('load', () => {
            requestAnimationFrame(() => {
                const pc = document.getElementById('pageContainer');
                pc.classList.remove('opacity-0','translate-y-4');
            });
        });

        // Anchor navigation transition
        const container = document.getElementById('pageContainer');
        function smoothSectionNav(e) {
            const href = this.getAttribute('href');
            if (!href || !href.startsWith('#')) return; // external or root
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            // fade out then scroll then fade in
            container.classList.add('opacity-0');
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => container.classList.remove('opacity-0'), 260);
            }, 180);
        }
        document.querySelectorAll('a[href^="#"]').forEach(a => a.addEventListener('click', smoothSectionNav));

        // Scroll driven reveal animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });

        document.querySelectorAll('[data-animate]')
            .forEach(el => {
                el.classList.add('opacity-0','translate-y-6','transition-all','duration-700');
                observer.observe(el);
            });
    </script>
    @stack('scripts')
</body>
</html>

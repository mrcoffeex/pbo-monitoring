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

    <main class="relative">
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
    </script>
    @stack('scripts')
</body>
</html>

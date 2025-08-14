{{-- Footer Partial --}}
{{-- resources/views/partials/footer.blade.php --}}
<footer class="py-12 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/50 transition-colors text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 md:grid-cols-3">
        <div>
            <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">PBO Monitoring</h3>
            <p class="text-gray-600 dark:text-gray-400 text-xs leading-relaxed">
                Transparent project lifecycle & budget oversight platform.
            </p>
        </div>
        <div>
            <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Resources</h3>
            <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                <li><a href="#features" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Features</a></li>
                <li><a href="#dev-team" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Dev Team</a></li>
                <li><a href="#" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Documentation</a></li>
            </ul>
        </div>
        <div>
            <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Legal</h3>
            <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                <li><a href="#" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="mt-10 pt-6 text-center text-xs text-gray-500 dark:text-gray-500">
        © {{ now()->year }} {{ config('app.name', 'Laravel') }}. All rights reserved.
    </div>
</footer>

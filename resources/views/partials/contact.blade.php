{{-- Contact Section Partial --}}
{{-- resources/views/partials/contact.blade.php --}}
<section id="contact" class="py-20 bg-gray-50 dark:bg-gray-900/50 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Contact</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Reach us via the details below.</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contact Details</h3>
                <ul class="mt-4 space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-primary-600 dark:text-primary-400">
                            <x-heroicon-o-envelope class="h-5 w-5" />
                        </span>
                        <a href="mailto:info@example.com"
                           class="text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            pbodavsur@gmail.com
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-primary-600 dark:text-primary-400">
                            <x-heroicon-o-phone class="h-5 w-5" />
                        </span>
                        <a href="tel:+63123456789"
                           class="text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">
                            +63 970 557 1417
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-primary-600 dark:text-primary-400">
                            <x-heroicon-o-clock class="h-5 w-5" />
                        </span>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Mon–Fri, 8:00 AM – 5:00 PM
                        </p>
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Office</h3>
                <div class="mt-4 flex items-start gap-3">
                    <span class="mt-0.5 text-primary-600 dark:text-primary-400">
                        <x-heroicon-o-map-pin class="h-5 w-5" />
                    </span>
                    <address class="not-italic text-sm text-gray-700 dark:text-gray-300">
                        Provincial Budget Office<br />
                        2/F Provincial Capitol Complex<br />
                        City, Province 1234, Philippines
                    </address>
                </div>
                <a href="https://maps.app.goo.gl/5nGs5Co4GuMXADGY8" target="_blank" rel="noopener"
                   class="mt-4 inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:underline">
                    <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                    View on Google Maps
                </a>
            </div>
        </div>
    </div>
</section>

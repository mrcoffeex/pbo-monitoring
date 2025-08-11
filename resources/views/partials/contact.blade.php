{{-- Contact Section Partial --}}
{{-- resources/views/partials/contact.blade.php --}}
<section id="contact" class="py-24 bg-gray-50 dark:bg-gray-900/50 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
            Ready to Modernize Budget Oversight?
        </h2>
        <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Join a transparent lifecycle platform powered by structured data & secure logging.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ route('filament.admin.auth.login') }}"
               class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 dark:bg-pink-600 dark:hover:bg-pink-500 text-white px-8 h-12 rounded-xl text-sm font-semibold shadow-sm shadow-pink-600/30 transition-colors">
                Access Dashboard
            </a>
            <a href="#features"
               class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 px-8 h-12 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors">
                Explore Features
            </a>
        </div>
    </div>
</section>

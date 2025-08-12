<section id="dev-team" class="py-20 bg-white dark:bg-gray-950/40 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Development Team</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Building and maintaining the platform.</p>
        </div>
        <div class="mt-12 grid gap-8 md:grid-cols-3 font-google-code">
            @php
                $team = [
                    [
                        'name' => 'Kent John Gocotano',
                        'role' => 'Full Stack Developer',
                        'bio'  => 'VILT - Filament - MySQL - WSL - Docker',
                        'img'  => 'https://ui-avatars.com/api/?name=KJG&background=6366F1&color=fff',
                        'links' => [
                            'github'   => 'https://github.com/mrcoffeex',
                            'linkedin' => 'https://www.linkedin.com/in/kentjohngo',
                        ],
                    ],
                ];
                $iconClasses = 'h-5 w-5';
            @endphp
            @foreach($team as $m)
                <figure class="relative bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 flex flex-col gap-4 transition-colors">
                    <div class="flex items-center gap-4">
                        <img src="{{ $m['img'] }}" alt="{{ $m['name'] }} avatar"
                             class="h-14 w-14 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover" loading="lazy" />
                        <div>
                            <figcaption class="font-semibold text-gray-900 dark:text-gray-100">{{ $m['name'] }}</figcaption>
                            <p class="text-xs text-primary-600 dark:text-primary-400 font-medium">{{ $m['role'] }}</p>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $m['bio'] }}</p>
                    @if(!empty($m['links']))
                        <div class="flex items-center gap-3 pt-2">
                            @if(isset($m['links']['github']))
                                <a href="{{ $m['links']['github'] }}" target="_blank" rel="noopener"
                                   class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                   aria-label="GitHub">
                                    <svg class="{{ $iconClasses }} text-gray-600 dark:text-gray-300 group-hover:text-gray-800 dark:group-hover:text-gray-200" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M12 2C6.477 2 2 6.589 2 12.223c0 4.482 2.865 8.281 6.839 9.63.5.095.683-.222.683-.49 0-.242-.01-1.04-.014-1.886-2.782.615-3.369-1.2-3.369-1.2-.455-1.175-1.11-1.488-1.11-1.488-.907-.633.07-.62.07-.62 1.002.072 1.53 1.05 1.53 1.05.892 1.556 2.341 1.107 2.91.846.092-.663.35-1.107.636-1.362-2.22-.258-4.555-1.128-4.555-5.016 0-1.108.39-2.013 1.03-2.722-.103-.258-.447-1.297.098-2.704 0 0 .84-.272 2.75 1.04A9.349 9.349 0 0 1 12 6.82c.85.004 1.705.116 2.504.338 1.909-1.312 2.748-1.04 2.748-1.04.546 1.407.202 2.446.1 2.704.64.709 1.028 1.614 1.028 2.722 0 3.898-2.339 4.756-4.566 5.008.359.319.678.949.678 1.915 0 1.382-.013 2.497-.013 2.838 0 .27.181.588.688.488A10.024 10.024 0 0 0 22 12.223C22 6.589 17.522 2 12 2Z" />
                                    </svg>
                                </a>
                            @endif
                            @if(isset($m['links']['linkedin']))
                                <a href="{{ $m['links']['linkedin'] }}" target="_blank" rel="noopener"
                                   class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-sky-400/60 dark:border-sky-500/50 hover:bg-sky-50 dark:hover:bg-sky-900/30 transition-colors"
                                   aria-label="LinkedIn">
                                    <svg class="{{ $iconClasses }} text-sky-600 dark:text-sky-400 group-hover:text-sky-700 dark:group-hover:text-sky-300" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 3A2 2 0 0 1 21 5V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V5A2 2 0 0 1 5 3H19ZM8.339 9.667H6.167V17.5H8.339V9.667ZM8.5 7.389C8.488 6.746 7.997 6.25 7.228 6.25C6.46 6.25 5.944 6.746 5.944 7.389C5.944 8.02 6.447 8.528 7.203 8.528H7.216C7.997 8.528 8.5 8.02 8.5 7.389ZM17.5 12.667C17.5 10.39 16.284 9.5 14.772 9.5C13.55 9.5 12.964 10.17 12.627 10.667V9.667H10.455C10.483 10.284 10.455 17.5 10.455 17.5H12.627V12.75C12.627 12.56 12.64 12.37 12.697 12.237C12.852 11.86 13.2 11.47 13.75 11.47C14.464 11.47 14.772 12.01 14.772 12.82V17.5H16.944L16.957 12.667H17.5Z"/>
                                    </svg>
                                </a>
                            @endif
                            @if(isset($m['links']['email']))
                                <a href="{{ $m['links']['email'] }}"
                                   class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-emerald-400/60 dark:border-emerald-500/50 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                   aria-label="Email">
                                    <svg class="{{ $iconClasses }} text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 7.5 12 13l9-5.5M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5A2 2 0 0 0 3 7v10a2 2 0 0 0 2 2Z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                    <span class="absolute -top-3 -left-3 h-9 w-9 rounded-xl bg-primary-600 text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-primary-600/30">
                        Dev
                    </span>
                </figure>
            @endforeach
        </div>
    </div>
</section>

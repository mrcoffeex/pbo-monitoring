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
                        'bio'  => 'VILT - TALL - MySQL - WSL - Docker',
                        'img'  => 'https://ui-avatars.com/api/?name=KJG&background=6366F1&color=fff',
                        'links' => [
                            'github'   => 'https://github.com/mrcoffeex',
                            'linkedin' => 'https://www.linkedin.com/in/kentjohngo',
                        ],
                    ],
                    [
                        'name' => 'Bhengie Mark Tubiano',
                        'role' => 'Quality Assurance',
                        'bio'  => 'CPA',
                        'img'  => 'https://ui-avatars.com/api/?name=BMT&background=4acf4d&color=fff',
                        'links' => [
                            'facebook' => 'https://www.facebook.com/matthew.tubiano',
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
                                    <x-heroicon-o-command-line class="{{ $iconClasses }} text-gray-600 dark:text-gray-300 group-hover:text-gray-800 dark:group-hover:text-gray-200" />
                                </a>
                            @endif

                            @if(isset($m['links']['linkedin']))
                                <a href="{{ $m['links']['linkedin'] }}" target="_blank" rel="noopener"
                                   class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-sky-400/60 dark:border-sky-500/50 hover:bg-sky-50 dark:hover:bg-sky-900/30 transition-colors"
                                   aria-label="LinkedIn">
                                    <x-heroicon-o-briefcase class="{{ $iconClasses }} text-sky-600 dark:text-sky-400 group-hover:text-sky-700 dark:group-hover:text-sky-300" />
                                </a>
                            @endif

                            @if(isset($m['links']['facebook']))
                                <a href="{{ $m['links']['facebook'] }}" target="_blank" rel="noopener"
                                   class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-blue-400/60 dark:border-blue-500/50 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                   aria-label="Facebook">
                                    <x-heroicon-o-globe-alt class="{{ $iconClasses }} text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300" />
                                </a>
                            @endif

                            @if(isset($m['links']['email']))
                                <a href="{{ $m['links']['email'] }}"
                                   class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-emerald-400/60 dark:border-emerald-500/50 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                   aria-label="Email">
                                    <x-heroicon-o-envelope class="{{ $iconClasses }} text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-300" />
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

<section id="team" class="border-t border-hairline bg-canvas py-16" aria-labelledby="team-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 id="team-heading" class="landing-section-title">The team</h2>
            <p class="landing-section-sub">The people who lead, check, and maintain this system for the Provincial Budget Office.</p>
        </div>

        <div class="mt-10 grid gap-4 md:grid-cols-3">
            @php
                $team = [
                    [
                        'name' => 'Dessamie Sanchez, CPA',
                        'role' => 'Project Leader',
                        'bio'  => 'Provincial Budget Officer. Sets direction and signs off on how the office uses this system.',
                        'img'  => 'https://ui-avatars.com/api/?name=DS&background=222222&color=fff',
                        'links' => ['facebook' => '#'],
                    ],
                    [
                        'name' => 'Bhengie Mark Tubiano',
                        'role' => 'Quality Assurance',
                        'bio'  => 'Head of Budget Execution. Reviews records so project data stays accurate and complete.',
                        'img'  => 'https://ui-avatars.com/api/?name=BMT&background=222222&color=fff',
                        'links' => ['facebook' => '#'],
                    ],
                    [
                        'name' => 'Kent John Gocotano',
                        'role' => 'Full Stack Developer',
                        'bio'  => 'Builds and maintains the application so offices can use it every day.',
                        'img'  => 'https://ui-avatars.com/api/?name=KJG&background=6a6a6a&color=fff',
                        'links' => [
                            'github'   => 'https://github.com/mrcoffeex',
                            'linkedin' => 'https://www.linkedin.com/in/kentjohngo',
                        ],
                    ],
                ];
            @endphp
            @foreach($team as $m)
                <figure class="landing-card flex flex-col gap-5 p-6">
                    <div class="flex items-center gap-4">
                        <img src="{{ $m['img'] }}" alt=""
                             class="h-16 w-16 rounded-full object-cover" loading="lazy" width="64" height="64" />
                        <div>
                            <figcaption class="text-base font-semibold text-ink">{{ $m['name'] }}</figcaption>
                            <p class="mt-0.5 text-sm text-muted">{{ $m['role'] }}</p>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-body">{{ $m['bio'] }}</p>
                    @if(!empty($m['links']))
                        <div class="mt-auto flex items-center gap-2">
                            @if(isset($m['links']['github']))
                                <a href="{{ $m['links']['github'] }}" target="_blank" rel="noopener"
                                   class="icon-btn-circle !h-9 !w-9" aria-label="{{ $m['name'] }} on GitHub">
                                    <x-heroicon-o-command-line class="h-4 w-4" />
                                </a>
                            @endif
                            @if(isset($m['links']['linkedin']))
                                <a href="{{ $m['links']['linkedin'] }}" target="_blank" rel="noopener"
                                   class="icon-btn-circle !h-9 !w-9" aria-label="{{ $m['name'] }} on LinkedIn">
                                    <x-heroicon-o-briefcase class="h-4 w-4" />
                                </a>
                            @endif
                            @if(isset($m['links']['facebook']))
                                <a href="{{ $m['links']['facebook'] }}" target="_blank" rel="noopener"
                                   class="icon-btn-circle !h-9 !w-9" aria-label="{{ $m['name'] }} on Facebook">
                                    <x-heroicon-o-globe-alt class="h-4 w-4" />
                                </a>
                            @endif
                        </div>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</section>

<section id="dev-team" class="border-t border-hairline bg-canvas py-16 lg:py-[64px]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl" data-animate>
            <h2 class="landing-section-title">Development team</h2>
            <p class="landing-section-sub">Building and maintaining the platform.</p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @php
                $team = [
                    [
                        'name' => 'Dessamie Sanchez, CPA',
                        'role' => 'Project Leader',
                        'bio'  => 'CPA — Provincial Budget Officer',
                        'img'  => 'https://ui-avatars.com/api/?name=DS&background=ff385c&color=fff',
                        'links' => ['facebook' => '#'],
                    ],
                    [
                        'name' => 'Bhengie Mark Tubiano',
                        'role' => 'Quality Assurance',
                        'bio'  => 'CPA — Head of Budget Execution',
                        'img'  => 'https://ui-avatars.com/api/?name=BMT&background=222222&color=fff',
                        'links' => ['facebook' => '#'],
                    ],
                    [
                        'name' => 'Kent John Gocotano',
                        'role' => 'Full Stack Developer',
                        'bio'  => 'VILT — TALL — MySQL — WSL — Docker',
                        'img'  => 'https://ui-avatars.com/api/?name=KJG&background=6a6a6a&color=fff',
                        'links' => [
                            'github'   => 'https://github.com/mrcoffeex',
                            'linkedin' => 'https://www.linkedin.com/in/kentjohngo',
                        ],
                    ],
                ];
            @endphp
            @foreach($team as $m)
                <figure class="landing-card flex flex-col gap-5 p-6" data-animate>
                    <div class="flex items-center gap-4">
                        <img src="{{ $m['img'] }}" alt="{{ $m['name'] }} avatar"
                             class="h-14 w-14 rounded-full object-cover ring-2 ring-hairline" loading="lazy" />
                        <div>
                            <figcaption class="text-base font-semibold text-ink">{{ $m['name'] }}</figcaption>
                            <p class="text-sm font-medium text-muted">{{ $m['role'] }}</p>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-body">{{ $m['bio'] }}</p>
                    @if(!empty($m['links']))
                        <div class="flex items-center gap-2 pt-1">
                            @if(isset($m['links']['github']))
                                <a href="{{ $m['links']['github'] }}" target="_blank" rel="noopener"
                                   class="icon-btn-circle !h-9 !w-9" aria-label="GitHub">
                                    <x-heroicon-o-command-line class="h-4 w-4" />
                                </a>
                            @endif
                            @if(isset($m['links']['linkedin']))
                                <a href="{{ $m['links']['linkedin'] }}" target="_blank" rel="noopener"
                                   class="icon-btn-circle !h-9 !w-9" aria-label="LinkedIn">
                                    <x-heroicon-o-briefcase class="h-4 w-4" />
                                </a>
                            @endif
                            @if(isset($m['links']['facebook']))
                                <a href="{{ $m['links']['facebook'] }}" target="_blank" rel="noopener"
                                   class="icon-btn-circle !h-9 !w-9" aria-label="Facebook">
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

<section id="how-it-works" class="border-t border-hairline bg-canvas py-16" aria-labelledby="how-it-works-heading">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(20rem,1fr)] lg:items-start lg:gap-16 lg:px-8">
        <div>
            <h2 id="how-it-works-heading" class="landing-section-title">How it works</h2>
            <p class="landing-section-sub">Each project keeps one record as it moves through four stages. Open a project to see where it is and what still needs action.</p>

            <ol class="mt-10 divide-y divide-hairline-soft border-y border-hairline-soft">
                @php
                    $steps = [
                        [
                            'id' => 'projects',
                            'n' => '1',
                            'title' => 'Procurement',
                            'text' => 'Record bidding, awards, and purchase requests so the paper trail stays with the project.',
                        ],
                        [
                            'id' => 'budget',
                            'n' => '2',
                            'title' => 'Obligation',
                            'text' => 'Track allotments, releases, and remaining balances before work is paid.',
                        ],
                        [
                            'id' => 'implementation',
                            'n' => '3',
                            'title' => 'Implementation',
                            'text' => 'Follow physical progress and updates while the project is underway.',
                        ],
                        [
                            'id' => 'payments',
                            'n' => '4',
                            'title' => 'Payment',
                            'text' => 'See disbursements and what is still due, against the approved budget.',
                        ],
                    ];
                @endphp
                @foreach($steps as $step)
                    <li id="{{ $step['id'] }}" class="amenity-row scroll-mt-28">
                        <span class="step-index" aria-hidden="true">{{ $step['n'] }}</span>
                        <div>
                            <h3 class="text-base font-semibold text-ink">{{ $step['title'] }}</h3>
                            <p class="mt-1 text-base leading-relaxed text-body">{{ $step['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <aside class="reservation-card lg:sticky lg:top-24" aria-label="Sign in">
            <p class="text-[21px] font-bold leading-[1.43] text-ink">Ready to track a project?</p>
            <p class="mt-2 text-sm leading-relaxed text-muted">
                Sign in with your office account to open the dashboard. Access follows your role.
            </p>
            <a href="{{ route('filament.admin.auth.login') }}" class="btn-rausch mt-6 w-full">
                Sign in to the dashboard
            </a>
            <p class="mt-4 text-center text-sm text-muted">
                Need help?
                <a href="#contact" class="text-ink hover:underline">Contact the office</a>
            </p>
        </aside>
    </div>
</section>

<section id="audience" class="border-t border-hairline bg-canvas py-16" aria-labelledby="audience-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 id="audience-heading" class="landing-section-title">Who it's for</h2>
            <p class="landing-section-sub">Built for the offices that move a project from award to payment — each with the access their role needs.</p>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $audiences = [
                    ['title' => 'Budget staff', 'text' => 'Record allotments and obligations, then see what is still available to spend.'],
                    ['title' => 'Procurement', 'text' => 'Keep bidding, awards, and purchase requests with the same project file.'],
                    ['title' => 'Implementing offices', 'text' => 'Post progress updates so leadership can see work on the ground.'],
                    ['title' => 'Executives', 'text' => 'Open a dashboard view of status, balances, and payments without chasing folders.'],
                ];
            @endphp
            @foreach($audiences as $audience)
                <article class="landing-card p-5">
                    <h3 class="text-base font-semibold text-ink">{{ $audience['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-body">{{ $audience['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

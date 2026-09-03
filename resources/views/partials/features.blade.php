<section id="features" class="border-t border-hairline bg-surface-soft py-16" aria-labelledby="features-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 id="features-heading" class="landing-section-title">What you can do</h2>
            <p class="landing-section-sub">Built for daily office use — so you can find a status without asking around.</p>
        </div>

        <div class="mt-10 grid gap-x-16 md:grid-cols-2">
            @php
                $features = [
                    ['title' => 'See the whole project', 'text' => 'Procurement, budget, implementation, and payments sit on one record. You do not have to jump between separate files.', 'icon' => 'heroicon-o-squares-2x2'],
                    ['title' => 'Follow the timeline', 'text' => 'Check what already happened, what is pending, and where a project is waiting.', 'icon' => 'heroicon-o-clock'],
                    ['title' => 'Open only what you need', 'text' => 'Staff see the projects and actions their role allows. Sensitive records stay with the right offices.', 'icon' => 'heroicon-o-shield-check'],
                    ['title' => 'Keep a complete record', 'text' => 'Every change is logged. That supports review, audit, and public accountability.', 'icon' => 'heroicon-o-lock-closed'],
                ];
            @endphp
            @foreach($features as $f)
                <article class="amenity-row border-b border-hairline-soft">
                    <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-surface-strong text-ink">
                        <x-dynamic-component :component="$f['icon']" class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <div class="pb-3">
                        <h3 class="text-base font-semibold text-ink">{{ $f['title'] }}</h3>
                        <p class="mt-1 text-base leading-relaxed text-body">{{ $f['text'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

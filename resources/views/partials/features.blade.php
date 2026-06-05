<section id="features" class="border-t border-hairline bg-surface-soft py-16 lg:py-[64px]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl" data-animate>
            <h2 class="landing-section-title">Platform features</h2>
            <p class="landing-section-sub">Reduce friction and improve accountability across every project phase.</p>
        </div>

        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $features = [
                    ['title' => 'Unified tracking', 'text' => 'Monitor procurement, obligations, implementation, and payments in one workspace.', 'icon' => 'heroicon-o-squares-2x2'],
                    ['title' => 'Timeline insights', 'text' => 'Visualize trends and identify bottlenecks early with structured logs.', 'icon' => 'heroicon-o-clock'],
                    ['title' => 'Role control', 'text' => 'Granular permissions powered by policy and shield integration.', 'icon' => 'heroicon-o-shield-check'],
                    ['title' => 'Secure audit', 'text' => 'Immutable activity logging ensures transparency and compliance.', 'icon' => 'heroicon-o-lock-closed'],
                ];
            @endphp
            @foreach($features as $f)
                <article class="landing-card flex flex-col gap-4 p-6" data-animate>
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-surface-soft text-ink ring-1 ring-hairline">
                        <x-dynamic-component :component="$f['icon']" class="h-6 w-6" />
                    </div>
                    <h3 class="text-base font-semibold text-ink">{{ $f['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-muted">{{ $f['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="features" class="py-20 bg-gray-50 dark:bg-gray-900/50 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Platform Features</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Reduce friction and improve accountability.</p>
        </div>
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $features = [
                    ['title'=>'Unified Tracking','text'=>'Monitor procurement, obligations, implementation & payments in one workspace.','icon'=>'heroicon-o-squares-2x2','wrap'=>'bg-pink-50 dark:bg-pink-900/25 text-pink-600 dark:text-pink-400 ring-pink-100 dark:ring-pink-800'],
                    ['title'=>'Timeline Insights','text'=>'Visualize trends and identify bottlenecks early with structured logs.','icon'=>'heroicon-o-clock','wrap'=>'bg-pink-50 dark:bg-pink-900/25 text-pink-600 dark:text-pink-400 ring-pink-100 dark:ring-pink-800'],
                    ['title'=>'Role Control','text'=>'Granular permissions powered by policy & shield integration.','icon'=>'heroicon-o-shield-check','wrap'=>'bg-pink-50 dark:bg-pink-900/25 text-pink-600 dark:text-pink-400 ring-pink-100 dark:ring-pink-800'],
                    ['title'=>'Secure Audit','text'=>'Immutable activity logging ensures transparency & compliance.','icon'=>'heroicon-o-lock-closed','wrap'=>'bg-pink-50 dark:bg-pink-900/25 text-pink-600 dark:text-pink-400 ring-pink-100 dark:ring-pink-800'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="group relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 flex flex-col gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                    <div class="h-12 w-12 rounded-xl flex items-center justify-center ring-1 {{ $f['wrap'] }}">
                        <x-dynamic-component :component="$f['icon']" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $f['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $f['text'] }}</p>
                    <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-primary-400/0 via-primary-400/60 to-primary-400/0 opacity-0 group-hover:opacity-100 transition"></span>
                </div>
            @endforeach
        </div>
    </div>
</section>

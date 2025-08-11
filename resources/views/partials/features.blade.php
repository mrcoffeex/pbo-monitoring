<section id="features" class="py-20 bg-gray-50 dark:bg-gray-900/50 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Platform Features</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Reduce friction and improve accountability.</p>
        </div>
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $features = [
                    ['title'=>'Unified Tracking','text'=>'Monitor procurement, obligations, implementation & payments in one workspace.','icon'=>'
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3.75 3.75h5.5v5.5h-5.5v-5.5ZM14.75 3.75h5.5v5.5h-5.5v-5.5ZM14.75 14.75h5.5v5.5h-5.5v-5.5ZM3.75 14.75h5.5v5.5h-5.5v-5.5Z" />
                        </svg>','wrap'=>'bg-rose-50 dark:bg-rose-900/25 text-rose-600 dark:text-rose-400 ring-rose-100 dark:ring-rose-800'],
                    ['title'=>'Timeline Insights','text'=>'Visualize trends and identify bottlenecks early with structured logs.','icon'=>'
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v6l3.5 2.1M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" />
                        </svg>','wrap'=>'bg-amber-50 dark:bg-amber-900/25 text-amber-600 dark:text-amber-400 ring-amber-100 dark:ring-amber-800'],
                    ['title'=>'Role Control','text'=>'Granular permissions powered by policy & shield integration.','icon'=>'
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12.75 11.25 15 15 9.75M12 2.25l8.25 3v6.75c0 5.004-3.42 9.59-8.25 10.5-4.83-.91-8.25-5.496-8.25-10.5V5.25L12 2.25Z" />
                        </svg>','wrap'=>'bg-emerald-50 dark:bg-emerald-900/25 text-emerald-600 dark:text-emerald-400 ring-emerald-100 dark:ring-emerald-800'],
                    ['title'=>'Secure Audit','text'=>'Immutable activity logging ensures transparency & compliance.','icon'=>'
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0V10.5M6 10.5h12v9.75A1.75 1.75 0 0116.25 22H7.75A1.75 1.75 0 016 20.25V10.5Z" />
                        </svg>','wrap'=>'bg-indigo-50 dark:bg-indigo-900/25 text-indigo-600 dark:text-indigo-400 ring-indigo-100 dark:ring-indigo-800'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="group relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 flex flex-col gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                    <div class="h-12 w-12 rounded-xl flex items-center justify-center ring-1 {{ $f['wrap'] }}">
                        {!! $f['icon'] !!}
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $f['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $f['text'] }}</p>
                    <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-primary-400/0 via-primary-400/60 to-primary-400/0 opacity-0 group-hover:opacity-100 transition"></span>
                </div>
            @endforeach
        </div>
    </div>
</section>

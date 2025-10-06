<div>
    <div class="gap-y-8 py-8 flex items-center justify-between">
        <h2 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">Dashboard Overview</h2>

        <div class="w-48">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    wire:model.live="selectedYear"
                >
                    @foreach($this->getAvailableYears() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </div>

    @php
        $stats = $this->getStatsData();
    @endphp

    <!-- Stats Grid -->
    <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        <!-- Online Users -->
        <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 p-6 shadow-sm ring-1 ring-emerald-100 dark:from-emerald-950/50 dark:to-teal-950/50 dark:ring-emerald-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Online Users</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-950 dark:text-emerald-50">{{ $stats['onlineUsers'] }}</p>
                    <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                        {{ $stats['onlinePercent'] }}% of {{ number_format($stats['totalUsers']) }} total
                    </p>
                </div>
                <div class="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900/50">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Released Projects -->
        <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-sm ring-1 ring-blue-100 dark:from-blue-950/50 dark:to-indigo-950/50 dark:ring-blue-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Released Projects</p>
                    <p class="mt-2 text-3xl font-semibold text-blue-950 dark:text-blue-50">{{ number_format($stats['releasedProjects']) }}</p>
                    <p class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        of {{ number_format($stats['totalProjects']) }} total
                    </p>
                </div>
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/50">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Procurements -->
        <div class="rounded-xl bg-gradient-to-br from-purple-50 to-fuchsia-50 p-6 shadow-sm ring-1 ring-purple-100 dark:from-purple-950/50 dark:to-fuchsia-950/50 dark:ring-purple-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-700 dark:text-purple-300">Total Procurements</p>
                    <p class="mt-2 text-3xl font-semibold text-purple-950 dark:text-purple-50">{{ number_format($stats['totalProcurements']) }}</p>
                    <p class="mt-2 text-sm text-purple-600 dark:text-purple-400">
                        {{ $stats['procurementsWithNOA'] }} with NOA
                    </p>
                </div>
                <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/50">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Procurements with NTP -->
        <div class="rounded-xl bg-gradient-to-br from-cyan-50 to-sky-50 p-6 shadow-sm ring-1 ring-cyan-100 dark:from-cyan-950/50 dark:to-sky-950/50 dark:ring-cyan-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-cyan-700 dark:text-cyan-300">With NTP</p>
                    <p class="mt-2 text-3xl font-semibold text-cyan-950 dark:text-cyan-50">{{ number_format($stats['procurementsWithNTP']) }}</p>
                    <p class="mt-2 text-sm text-cyan-600 dark:text-cyan-400">
                        Notice to Proceed issued
                    </p>
                </div>
                <div class="rounded-full bg-cyan-100 p-3 dark:bg-cyan-900/50">
                    <svg class="h-6 w-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- With Purchase Requests -->
        <div class="rounded-xl bg-gradient-to-br from-green-50 to-lime-50 p-6 shadow-sm ring-1 ring-green-100 dark:from-green-950/50 dark:to-lime-950/50 dark:ring-green-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">With Purchase Requests</p>
                    <p class="mt-2 text-3xl font-semibold text-green-950 dark:text-green-50">{{ number_format($stats['withPurchaseRequests']) }}</p>
                    <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                        {{ $stats['withPurchaseRequestsPercent'] }}% of projects
                    </p>
                </div>
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/50">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- No Purchase Request -->
        <div class="rounded-xl bg-gradient-to-br from-red-50 to-rose-50 p-6 shadow-sm ring-1 ring-red-100 dark:from-red-950/50 dark:to-rose-950/50 dark:ring-red-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">No Purchase Request</p>
                    <p class="mt-2 text-3xl font-semibold text-red-950 dark:text-red-50">{{ number_format($stats['noPurchaseRequestCount']) }}</p>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ $stats['noPurchaseRequestPercent'] }}% of projects
                    </p>
                </div>
                <div class="rounded-full bg-red-100 p-3 dark:bg-red-900/50">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Payments -->
        <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 p-6 shadow-sm ring-1 ring-amber-100 dark:from-amber-950/50 dark:to-orange-950/50 dark:ring-amber-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-300">Total Payments</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-950 dark:text-amber-50">₱{{ number_format($stats['paymentsTotalAmount'], 2) }}</p>
                    <p class="mt-2 text-sm {{ $stats['paymentsGrowthPercent'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $stats['paymentsGrowthPercent'] >= 0 ? '+' : '' }}{{ $stats['paymentsGrowthPercent'] }}% from last week
                    </p>
                </div>
                <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900/50">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <!-- Payment Chart -->
            <div class="mt-4 h-20">
                <canvas id="paymentChart" class="w-full"></canvas>
            </div>
        </div>

        <!-- Projects without Purchase Requests Bar -->
        <div class="rounded-xl bg-gradient-to-br from-slate-50 to-gray-50 p-6 shadow-sm ring-1 ring-slate-100 dark:from-slate-950/50 dark:to-gray-950/50 dark:ring-slate-800/50">
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Project Status</p>
            <div class="mt-4 space-y-3">
                <div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">With PR</span>
                        <span class="font-medium text-slate-950 dark:text-slate-50">{{ $stats['withPurchaseRequestsPercent'] }}%</span>
                    </div>
                    <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-green-500 transition-all duration-500 ease-out" style="width: {{ $stats['withPurchaseRequestsPercent'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Without PR</span>
                        <span class="font-medium text-slate-950 dark:text-slate-50">{{ $stats['noPurchaseRequestPercent'] }}%</span>
                    </div>
                    <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-full bg-gradient-to-r from-red-500 to-rose-500 transition-all duration-500 ease-out" style="width: {{ $stats['noPurchaseRequestPercent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initPaymentChart();
        });

        document.addEventListener('livewire:navigated', function() {
            initPaymentChart();
        });

        function initPaymentChart() {
            const canvas = document.getElementById('paymentChart');
            if (!canvas) return;

            // Destroy existing chart if it exists
            if (canvas.chart) {
                canvas.chart.destroy();
            }

            const ctx = canvas.getContext('2d');
            const chartData = @json($stats['paymentChartData']);

            canvas.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['6d ago', '5d ago', '4d ago', '3d ago', '2d ago', 'Yesterday', 'Today'],
                    datasets: [{
                        label: 'Payments',
                        data: chartData,
                        borderColor: 'rgb(245, 158, 11)',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        // Reinitialize chart on Livewire updates
        Livewire.hook('message.processed', (message, component) => {
            if (component.name === 'filament.pages.custom-dashboard') {
                setTimeout(() => initPaymentChart(), 100);
            }
        });
    </script>
    @endpush
</div>

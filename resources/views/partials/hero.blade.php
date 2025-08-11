<section class="relative overflow-hidden" id="hero">
    <div class="absolute inset-0 pointer-events-none bg-gradient-to-b from-gray-50 via-white to-white dark:from-gray-900/20 dark:via-gray-950 dark:to-gray-950 transition-colors"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                <span class="bg-gradient-to-r from-pink-600 via-pink-500 to-pink-600 dark:from-pink-400 dark:via-pink-300 dark:to-pink-500 bg-clip-text text-transparent drop-shadow-sm">
                    Real-Time Provincial Infrastructure Monitoring
                </span>
            </h1>
            <p class="mt-6 text-xl sm:text-2xl leading-relaxed text-gray-700 dark:text-gray-300 max-w-2xl font-medium">
                Streamline procurement, obligation, implementation, and payment tracking with actionable transparency.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('filament.admin.auth.login') }}"
                   class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 dark:bg-pink-600 dark:hover:bg-pink-500 text-white px-6 h-11 rounded-xl text-sm font-semibold shadow-sm shadow-pink-600/30 transition-colors">
                    Login
                    <x-heroicon-o-arrow-right class="h-5 w-5"/>
                </a>
                <a href="#features"
                   class="inline-flex items-center gap-2 border border-gray-300 dark:border-gray-700 hover:border-pink-400 dark:hover:border-pink-500 hover:text-pink-600 dark:hover:text-pink-400 px-6 h-11 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors">
                    Learn More
                </a>
            </div>
            <div class="mt-10 flex flex-wrap items-center gap-6 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Data Sync
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-pink-500"></span>
                    Role-Based Access
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    Secure Logging
                </div>
            </div>
        </div>
        <!-- Preview Figures -->
        <div class="hidden lg:flex relative h-full w-full justify-center">
            <!-- Decorative gradient blobs -->
            <div class="absolute -top-20 -right-20 h-72 w-72 bg-pink-400/20 dark:bg-pink-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -left-10 h-64 w-64 bg-indigo-400/10 dark:bg-indigo-500/10 rounded-full blur-2xl"></div>

            <!-- Desktop mockup -->
            <div class="relative group w-[520px] max-w-full">
                <div class="relative rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/60 backdrop-blur shadow-xl shadow-pink-500/5 overflow-hidden ring-1 ring-white/50 dark:ring-gray-800">
                    <div class="flex items-center gap-1 px-4 h-9 bg-gray-100/70 dark:bg-gray-800/70 border-b border-gray-200 dark:border-gray-700">
                        <span class="h-3 w-3 rounded-full bg-red-400"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                        <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                        <span class="ml-4 text-[10px] font-medium text-gray-500 dark:text-gray-400 tracking-wide">Dashboard Preview</span>
                    </div>
                    <div class="aspect-[16/9] w-full bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 flex items-start justify-center relative py-5">
                        <div class="absolute inset-0 opacity-40 bg-[radial-gradient(circle_at_30%_30%,theme(colors.pink.400/25),transparent)]"></div>
                        <div class="grid grid-cols-3 gap-5 w-11/12 text-[11px] leading-tight">
                            <!-- Left: KPI / mini cards -->
                            <div class="col-span-2 space-y-4">
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-1">
                                        <x-heroicon-o-chart-bar class="h-4 w-4 text-pink-500"/> KPI Snapshot
                                    </h3>
                                    <div class="mt-2 grid grid-cols-3 gap-3">
                                        <div class="rounded-lg bg-white dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 p-2 shadow-sm">
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Projects</p>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">128</p>
                                            <div class="mt-1 h-1.5 rounded-full bg-gray-200 dark:bg-gray-600">
                                                <div class="h-full w-2/3 rounded-full bg-gradient-to-r from-pink-500 to-pink-400"></div>
                                            </div>
                                        </div>
                                        <div class="rounded-lg bg-white dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 p-2 shadow-sm">
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Procurements</p>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">54</p>
                                            <div class="mt-1 h-1.5 rounded-full bg-gray-200 dark:bg-gray-600">
                                                <div class="h-full w-1/2 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-400"></div>
                                            </div>
                                        </div>
                                        <div class="rounded-lg bg-white dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 p-2 shadow-sm">
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Payments</p>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">91%</p>
                                            <div class="mt-1 h-1.5 rounded-full bg-gray-200 dark:bg-gray-600">
                                                <div class="h-full w-5/6 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Overview -->
                                <div class="rounded-xl bg-white dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 p-3 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Implementation Progress</p>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-pink-100 dark:bg-pink-500/10 text-pink-600 dark:text-pink-300 font-medium">Live</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div>
                                            <div class="flex justify-between text-[10px] text-gray-500 dark:text-gray-400"><span>Roads</span><span>72%</span></div>
                                            <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                                <div class="h-full w-[72%] bg-gradient-to-r from-pink-500 to-pink-400"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between text-[10px] text-gray-500 dark:text-gray-400"><span>Schools</span><span>56%</span></div>
                                            <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                                <div class="h-full w-[56%] bg-gradient-to-r from-indigo-500 to-indigo-400"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between text-[10px] text-gray-500 dark:text-gray-400"><span>Health</span><span>88%</span></div>
                                            <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                                <div class="h-full w-[88%] bg-gradient-to-r from-emerald-500 to-emerald-400"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Activity Feed -->
                                <div class="rounded-xl bg-white dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 p-3 shadow-sm">
                                    <div class="flex items-center gap-1 mb-1">
                                        <x-heroicon-o-bolt class="h-4 w-4 text-pink-500"/>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Recent Activity</p>
                                    </div>
                                    <ul class="space-y-1">
                                        <li class="flex items-start gap-2">
                                            <span class="mt-0.5 h-2 w-2 rounded-full bg-emerald-400"></span>
                                            <p class="text-[10px] text-gray-600 dark:text-gray-300"><strong class="text-gray-800 dark:text-gray-100">PR-2025-091</strong> awarded to <span class="text-pink-600 dark:text-pink-300">ABC Builders</span></p>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="mt-0.5 h-2 w-2 rounded-full bg-pink-400"></span>
                                            <p class="text-[10px] text-gray-600 dark:text-gray-300">Payment batch <strong class="text-gray-800 dark:text-gray-100">#147</strong> processed</p>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="mt-0.5 h-2 w-2 rounded-full bg-indigo-400"></span>
                                            <p class="text-[10px] text-gray-600 dark:text-gray-300">New project <strong class="text-gray-800 dark:text-gray-100">Bridge Repair (North)</strong></p>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Right: Mini table / status -->
                            <div class="space-y-4">
                                <div class="rounded-xl bg-white dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 p-3 shadow-sm">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Pending Approvals</p>
                                    <ul class="space-y-1.5">
                                        <li class="flex justify-between text-[10px] text-gray-600 dark:text-gray-300"><span>PR Control</span><span class="px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 font-medium">3</span></li>
                                        <li class="flex justify-between text-[10px] text-gray-600 dark:text-gray-300"><span>Obligations</span><span class="px-1.5 py-0.5 rounded bg-pink-100 dark:bg-pink-500/10 text-pink-700 dark:text-pink-300 font-medium">5</span></li>
                                        <li class="flex justify-between text-[10px] text-gray-600 dark:text-gray-300"><span>Payments</span><span class="px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 font-medium">2</span></li>
                                    </ul>
                                </div>
                                <div class="rounded-xl bg-white dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 p-3 shadow-sm">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">System Health</p>
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-[10px] text-gray-600 dark:text-gray-300"><span>API</span><span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Up</span></div>
                                        <div class="flex items-center justify-between text-[10px] text-gray-600 dark:text-gray-300"><span>Queue</span><span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Healthy</span></div>
                                        <div class="flex items-center justify-between text-[10px] text-gray-600 dark:text-gray-300"><span>DB</span><span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>OK</span></div>
                                    </div>
                                </div>
                                <div class="rounded-xl bg-gradient-to-r from-pink-500/15 via-pink-400/15 to-pink-500/15 dark:from-pink-500/10 dark:via-pink-400/10 dark:to-pink-500/10 border border-pink-400/30 dark:border-pink-500/20 p-3 shadow-sm">
                                    <p class="text-[11px] font-semibold text-pink-700 dark:text-pink-300 mb-1">On Track</p>
                                    <p class="text-[10px] text-gray-600 dark:text-gray-300">91% of current payment schedules are within target cycle time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Glow -->
                <div class="absolute inset-0 rounded-2xl ring-1 ring-pink-500/30 dark:ring-pink-400/30 pointer-events-none opacity-0 group-hover:opacity-100 transition"></div>
            </div>

            <!-- Mobile mockup -->
            <div class="absolute -right-10 bottom-4 w-44">
                <div class="relative rounded-[2rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-2xl shadow-pink-500/10 p-3 aspect-[9/18] flex flex-col overflow-hidden">
                    <div class="h-5 flex items-center justify-center">
                        <div class="h-1.5 w-14 rounded-full bg-gray-300 dark:bg-gray-700"></div>
                    </div>
                    <div class="mt-2 space-y-3 text-[10px] leading-tight">
                        <p class="text-[11px] font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-1"><x-heroicon-o-sparkles class="h-4 w-4 text-pink-500"/> Quick Stats</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-lg bg-white dark:bg-gray-800/70 border border-gray-200 dark:border-gray-600 p-2">
                                <p class="text-[9px] text-gray-500 dark:text-gray-400">Projects</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">128</p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-gray-800/70 border border-gray-200 dark:border-gray-600 p-2">
                                <p class="text-[9px] text-gray-500 dark:text-gray-400">Payments</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">91%</p>
                            </div>
                            <div class="col-span-2 rounded-lg bg-gradient-to-r from-pink-500/15 to-pink-400/15 dark:from-pink-500/10 dark:to-pink-400/10 border border-pink-400/30 dark:border-pink-500/20 p-2">
                                <p class="text-[9px] uppercase tracking-wide text-pink-600 dark:text-pink-300 font-medium">Live Feed</p>
                                <ul class="mt-1 space-y-0.5">
                                    <li class="flex gap-1"><span class="h-1.5 w-1.5 mt-1 rounded-full bg-emerald-500"></span><span class="text-gray-600 dark:text-gray-300">PR #091 awarded</span></li>
                                    <li class="flex gap-1"><span class="h-1.5 w-1.5 mt-1 rounded-full bg-pink-500"></span><span class="text-gray-600 dark:text-gray-300">Payment batch #147</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-[9px] text-gray-500 dark:text-gray-400"><span>Roads</span><span>72%</span></div>
                            <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden"><div class="h-full w-[72%] bg-gradient-to-r from-pink-500 to-pink-400"></div></div>
                        </div>
                        <div class="mt-1 flex justify-center">
                            <div class="h-9 w-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <x-heroicon-o-globe-alt class="h-4 w-4 text-gray-500 dark:text-gray-400"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

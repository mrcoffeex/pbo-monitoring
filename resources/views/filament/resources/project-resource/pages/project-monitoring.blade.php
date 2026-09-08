@php
    $project = $this->project;
    $summary = $this->summary;
    $stages = $this->stages;
    $recentActivities = $this->recentActivities;
    $defaultStage = $this->defaultStage();
    $stageLabels = collect($stages)->mapWithKeys(fn (array $stage): array => [$stage['key'] => $stage['label']])->all();
    $stageLabels['overview'] = 'Overview';
    $visibleStageKeys = collect($stages)->pluck('key')->all();

    $preProcurements = $project->pre_procurements;
    $purchaseRequests = $project->purchase_requests;
    $twgs = $project->technical_working_groups;
    $pmoControls = $project->procurement_controls;
    $prControls = $project->purchase_request_controls;
    $procurements = $project->procurements;
    $obrs = $project->obligation_requests;
    $implementations = $project->implementations;
    $payments = $project->payments;

    $cardWrap = 'space-y-2 rounded-xl bg-gray-50 p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10';
    $sectionBody = 'space-y-3 text-sm leading-6 text-gray-950 dark:text-gray-100';
    $overviewSpan = [
        'pre' => 'process-span-33',
        'pr' => 'process-span-33',
        'twg' => 'process-span-33',
        'pmo' => 'process-span-40',
        'prc' => 'process-span-60',
        'proc' => 'process-span-100',
        'obr' => 'process-span-33',
        'impl' => 'process-span-33',
        'pay' => 'process-span-33',
    ];
@endphp

<x-filament-panels::page>
    <div
        class="relative space-y-5"
        x-data="{
            activeStage: @js($defaultStage),
            labels: @js($stageLabels),
            select(stage) {
                this.activeStage = stage;
                this.$nextTick(() => {
                    this.$refs.stagePanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            },
            shows(stage) {
                return this.activeStage === 'overview' || this.activeStage === stage;
            },
            get isOverview() {
                return this.activeStage === 'overview';
            },
            get selectedLabel() {
                return this.labels[this.activeStage] ?? 'Overview';
            },
        }"
    >
        {{-- Header --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:px-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-filament::badge :color="$summary['statusColor']">
                                {{ $summary['statusLabel'] ?: 'No status' }}
                            </x-filament::badge>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                CY {{ $project->year }}
                            </span>
                        </div>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">
                            {{ $project->name }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ $project->code ?: 'No responsibility center' }}
                            <span class="mx-2 text-gray-300 dark:text-gray-600">·</span>
                            {{ $summary['contractor'] }}
                            @if ($project->user?->name)
                                <span class="mx-2 text-gray-300 dark:text-gray-600">·</span>
                                {{ $project->user->name }}
                            @endif
                        </p>
                        @if ($summary['typeLabels'] !== [] || $summary['fundLabels'] !== [])
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($summary['typeLabels'] as $typeLabel)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">{{ $typeLabel }}</span>
                                @endforeach
                                @foreach ($summary['fundLabels'] as $fundLabel)
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">{{ $fundLabel }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 xl:grid-cols-6 sm:p-5">
                <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300">Appropriation</p>
                    <p class="mt-2 text-sm font-semibold text-gray-950 dark:text-white sm:text-base">₱{{ number_format($summary['appropriation'], 2) }}</p>
                </div>
                <div class="rounded-xl bg-indigo-50 p-4 dark:bg-indigo-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">Allotment</p>
                    <p class="mt-2 text-sm font-semibold text-gray-950 dark:text-white sm:text-base">₱{{ number_format($summary['allotment'], 2) }}</p>
                </div>
                <div class="rounded-xl bg-fuchsia-50 p-4 dark:bg-fuchsia-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-fuchsia-600 dark:text-fuchsia-300">Contract</p>
                    <p class="mt-2 text-sm font-semibold text-gray-950 dark:text-white sm:text-base">₱{{ number_format($summary['contractAmount'], 2) }}</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">Paid</p>
                    <p class="mt-2 text-sm font-semibold text-gray-950 dark:text-white sm:text-base">₱{{ number_format($summary['totalPayment'], 2) }}</p>
                </div>
                <div class="rounded-xl bg-amber-50 p-4 dark:bg-amber-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-300">Balance</p>
                    <p class="mt-2 text-sm font-semibold text-gray-950 dark:text-white sm:text-base">₱{{ number_format($summary['balance'], 2) }}</p>
                </div>
                <div class="rounded-xl bg-cyan-50 p-4 dark:bg-cyan-500/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600 dark:text-cyan-300">Implementation</p>
                    <p class="mt-2 text-sm font-semibold text-gray-950 dark:text-white sm:text-base">{{ rtrim(rtrim(number_format($summary['implPct'], 2), '0'), '.') }}%</p>
                </div>
            </div>
        </section>

        {{-- Stage switcher --}}
        <section class="sticky top-0 z-20 rounded-2xl bg-white/95 p-4 shadow-sm ring-1 ring-gray-950/5 backdrop-blur dark:bg-gray-900/95 dark:ring-white/10 sm:p-5">
            <div class="mb-3 flex flex-wrap items-end justify-between gap-2">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Process stages</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Viewing <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedLabel"></span>
                    </p>
                </div>
                <button
                    type="button"
                    class="text-sm font-semibold text-primary-600 hover:text-primary-500 dark:text-primary-400"
                    x-show="! isOverview"
                    x-cloak
                    x-on:click="select('overview')"
                >
                    Overview
                </button>
            </div>

            <div class="flex gap-2 overflow-x-auto pb-1">
                <button
                    type="button"
                    x-on:click="select('overview')"
                    class="shrink-0 rounded-xl border px-4 py-3 text-left transition"
                    :class="activeStage === 'overview'
                        ? 'border-primary-400 bg-primary-50 text-primary-900 ring-1 ring-primary-200 dark:border-primary-400/50 dark:bg-primary-500/20 dark:text-primary-100'
                        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200'"
                >
                    <span class="block text-[11px] font-bold uppercase tracking-wide opacity-70">View</span>
                    <span class="mt-1 block text-sm font-semibold">Overview</span>
                </button>

                @foreach ($stages as $stage)
                    <button
                        type="button"
                        data-stage="{{ $stage['key'] }}"
                        x-on:click="select('{{ $stage['key'] }}')"
                        class="min-w-[8rem] shrink-0 rounded-xl border px-3 py-2 text-left transition"
                        :class="activeStage === '{{ $stage['key'] }}'
                            ? 'border-primary-400 bg-primary-50 ring-1 ring-primary-200 dark:border-primary-400/50 dark:bg-primary-500/20'
                            : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800'"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-sm font-semibold leading-tight text-gray-950 dark:text-white">{{ $stage['label'] }}</span>
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ $stage['count'] }}</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>

        <div
            x-ref="stagePanel"
            data-overview-grid
            class="process-stage-grid scroll-mt-28"
            :class="{ 'is-overview': isOverview }"
        >
            {{-- Process details: all on Overview in a 2–3 card grid, one when a stage is selected --}}
            @if (in_array('pre', $visibleStageKeys, true))
            <div x-show="shows('pre')" class="min-w-0 {{ $overviewSpan['pre'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Pre-Procurement <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $preProcurements->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($preProcurements as $preProcurement)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="Remarks" :value="Str::limit($preProcurement->remarks, 60, '...')" :tooltip="$preProcurement->remarks" />
                                <x-item-badge label="Created By" :value="$preProcurement->user?->name" :isCreator="true" />
                                <x-item-badge label="Created Date" :value="$preProcurement->created_at" :isDay="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No pre-procurement records yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('pr', $visibleStageKeys, true))
            <div x-show="shows('pr')" class="min-w-0 {{ $overviewSpan['pr'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Purchase Requests <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $purchaseRequests->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($purchaseRequests as $pr)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="Received Date" :value="$pr->received_date" :isDay="true" />
                                <x-item-badge label="PR Number" :value="$pr->pr_number" />
                                <x-item-badge label="Remarks" :value="Str::limit($pr->remarks, 60, '...')" :tooltip="$pr->remarks" />
                                <x-item-badge label="Forwarded To TWG" :value="$pr->forward_twg_date" :isDay="true" />
                                <x-item-badge label="Created By" :value="$pr->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No purchase requests yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('twg', $visibleStageKeys, true))
            <div x-show="shows('twg')" class="min-w-0 {{ $overviewSpan['twg'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Technical Working Group <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $twgs->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($twgs as $twg)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="TWG Review Date" :value="$twg->review_date" :isDay="true" />
                                <x-item-badge label="TWG Review Remarks" :value="Str::limit($twg->review_remarks, 60, '...')" :tooltip="$twg->review_remarks" />
                                <x-item-badge label="Created By" :value="$twg->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No TWG reviews yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('pmo', $visibleStageKeys, true))
            <div x-show="shows('pmo')" class="min-w-0 {{ $overviewSpan['pmo'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">PMO Control <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $pmoControls->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($pmoControls as $pmoControl)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="Controlled Date" :value="$pmoControl->controlled_date" :isDay="true" />
                                <x-item-badge label="ABC" :value="$pmoControl->abc" :isMoney="true" />
                                <x-item-badge label="Remarks" :value="Str::limit($pmoControl->remarks, 60, '...')" :tooltip="$pmoControl->remarks" />
                                <x-item-badge label="Created By" :value="$pmoControl->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No PMO control records yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('prc', $visibleStageKeys, true))
            <div x-show="shows('prc')" class="min-w-0 {{ $overviewSpan['prc'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Purchase Request Control <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $prControls->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($prControls as $prc)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="PR Controlled Date" :value="$prc->controlled_date" :isDay="true" />
                                <x-item-badge label="PR Control Number" :value="$prc->control_number" />
                                <x-item-badge label="Amount" :value="$prc->amount" :isMoney="true" />
                                <x-item-badge label="Created By" :value="$prc->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No PR control records yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('proc', $visibleStageKeys, true))
            <div x-show="shows('proc')" class="min-w-0 {{ $overviewSpan['proc'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Procurement <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $procurements->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($procurements as $proc)
                            <div class="grid grid-cols-1 gap-x-4 gap-y-1 rounded-xl bg-gray-50 p-4 ring-1 ring-gray-950/5 md:grid-cols-2 dark:bg-gray-900 dark:ring-white/10">
                                <x-item-badge label="IB Number" :value="$proc->ib_number" />
                                <x-item-badge label="Pre Procurement Conference" :value="$proc->pre_procurement_conference" :isDay="true" />
                                <x-item-badge label="Pre Bid Conference" :value="$proc->pre_bid_conference" :isDay="true" />
                                <x-item-badge label="Bid Opening" :value="$proc->bid_opening" :isArray="true" />
                                <x-item-badge label="BER" :value="$proc->ber" :isDay="true" />
                                <x-item-badge label="Post Qua Date" :value="$proc->post_qua_date" :isDay="true" />
                                <x-item-badge label="Remarks" :value="Str::limit($proc->remarks, 60, '...')" :tooltip="$proc->remarks" />
                                <x-item-badge label="NOA Date Received" :value="$proc->noa_date_received" :isDay="true" />
                                <x-item-badge label="Contract Amount" :value="$proc->contract_amount" :isMoney="true" />
                                <x-item-badge label="Contractor" :value="Str::limit($proc->contractor, 50, '...')" />
                                <x-item-badge label="NTP Number" :value="$proc->ntp_number" />
                                <x-item-badge label="NTP Date" :value="$proc->ntp_date" :isDay="true" />
                                <x-item-badge label="Contract Duration" :value="$proc->contract_duration" :isDay="true" />
                                <x-item-badge label="Created By" :value="$proc->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No procurement records yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('obr', $visibleStageKeys, true))
            <div x-show="shows('obr')" class="min-w-0 {{ $overviewSpan['obr'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Obligation Request <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $obrs->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($obrs as $obr)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="OBR Controlled Date" :value="$obr->controlled_date" :isDay="true" />
                                <x-item-badge label="OBR Number" :value="$obr->number" />
                                <x-item-badge label="Amount" :value="$obr->amount" :isMoney="true" />
                                <x-item-badge label="Created By" :value="$obr->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No obligation requests yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('impl', $visibleStageKeys, true))
            <div x-show="shows('impl')" class="min-w-0 {{ $overviewSpan['impl'] }}">
                <x-filament::section class="h-full">
                    <x-slot name="heading">Implementation <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $implementations->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($implementations as $imp)
                            @if ($loop->first)
                                <div class="mb-1 grid grid-cols-1 gap-2 sm:grid-cols-3">
                                    <x-item-badge label="Start date" :value="$imp->start_date" :isDay="true" />
                                    <x-item-badge label="Completion date" :value="$imp->end_date" :isDay="true" />
                                    <x-item-badge label="Coordinates" :value="$imp->coordinates" />
                                </div>
                            @endif
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="Date" :value="$imp->date" :isDay="true" />
                                <x-item-badge
                                    label="Percentage"
                                    :value="is_numeric($imp->percentage)
                                        ? rtrim(rtrim(number_format($imp->percentage, 2, '.', ''), '0'), '.').' %'
                                        : $imp->percentage"
                                />
                                <x-item-badge label="Remarks" :value="Str::limit($imp->remarks, 60, '...')" :tooltip="$imp->remarks" />
                                <x-item-badge label="Created By" :value="$imp->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No implementation updates yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
            @endif

            @if (in_array('pay', $visibleStageKeys, true))
            <div x-show="shows('pay')" class="min-w-0 space-y-4 {{ $overviewSpan['pay'] }}">
                <x-filament::section>
                    <x-slot name="heading">Payments <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $payments->count() }})</span></x-slot>
                    <div class="{{ $sectionBody }}">
                        @forelse ($payments as $pay)
                            <div class="{{ $cardWrap }}">
                                <x-item-badge label="Type of Payment" :value="App\Enums\CustomOptions::PAYMENTS[$pay->type] ?? 'Unknown'" />
                                <x-item-badge label="Date of Payment" :value="$pay->date" :isDay="true" />
                                <x-item-badge label="Amount" :value="$pay->amount" :isMoney="true" />
                                <x-item-badge label="Payable Ref." :value="$pay->payable_reference" />
                                <x-item-badge label="Payment Ref." :value="$pay->payment_reference" />
                                <x-item-badge label="Check Number" :value="$pay->check_number" />
                                <x-item-badge label="Check Date" :value="$pay->check_date" :isDay="true" />
                                <x-item-badge label="Created By" :value="$pay->user?->name" :isCreator="true" />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">No payments yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Balance summary</x-slot>
                    <div class="{{ $sectionBody }}">
                        <div class="{{ $cardWrap }}">
                            <x-item-badge label="Total Payments" :value="$summary['totalPayment']" :isMoney="true" />
                            <x-item-badge label="Contract Amount" :value="$summary['contractAmount']" :isMoney="true" />
                            <x-item-badge label="Balance" :value="$summary['balance']" :isMoney="true" />
                        </div>
                    </div>
                </x-filament::section>
            </div>
            @endif
        </div>
    </div>

    <style>
        .process-stage-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            min-width: 0;
        }

        .process-stage-grid > * {
            min-width: 0;
            max-width: 100%;
        }

        @media (min-width: 1024px) {
            .process-stage-grid.is-overview {
                display: grid;
                grid-template-columns: repeat(15, minmax(0, 1fr));
                align-items: stretch;
            }

            .process-stage-grid.is-overview .process-span-33 {
                grid-column: span 5;
            }

            .process-stage-grid.is-overview .process-span-40 {
                grid-column: span 6;
            }

            .process-stage-grid.is-overview .process-span-60 {
                grid-column: span 9;
            }

            .process-stage-grid.is-overview .process-span-100 {
                grid-column: span 15;
            }
        }
    </style>
</x-filament-panels::page>

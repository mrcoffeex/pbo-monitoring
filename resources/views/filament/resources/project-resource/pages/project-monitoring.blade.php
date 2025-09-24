<x-filament::page>
    @php
        $project = $this->project->loadMissing([
            'purchase_requests.user',
            'technical_working_groups.user',
            'purchase_request_controls.user',
            'procurements.user',
            'obligation_requests.user',
            'implementations.user',
            'payments.user',
        ]);

        $purchaseRequests   = $project->purchase_requests;
        $twgs               = $project->technical_working_groups;
        $pmoControls        = $project->procurement_controls;
        $prControls         = $project->purchase_request_controls;
        $procurements       = $project->procurements;
        $obrs               = $project->obligation_requests;
        $implementations    = $project->implementations;
        $payments           = $project->payments;

        $appropriation = $project->appropriation;
        $allotment = $project->allotment;
        $totalPayment = $payments->sum('amount');
        $contractAmount = $procurements->sum('contract_amount');

        $contractors = $procurements->pluck('contractor')->filter()->unique()->values();
        $contractor = $contractors->count() ? ($contractors->count() === 1 ? $contractors->first() : $contractors->implode(', ')) : 'No Contractor';
        $unpaid       = $contractAmount - $totalPayment;
        $paymentPct   = $contractAmount > 0 ? round(($totalPayment / $contractAmount) * 100, 2) : 0;
        $latestImpl   = $implementations->sortByDesc('date')->first();
        $implPct      = ($latestImpl && is_numeric($latestImpl->percentage)) ? (float)$latestImpl->percentage : 0;

        $cardWrap = 'space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm';
        $sectionBody = 'text-sm whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4';
    @endphp

    <div class="flex flex-col sm:flex-row flex-wrap gap-4 mb-2">
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="text-[11px] uppercase tracking-wide text-blue-500 dark:text-blue-400 mb-2">Appropriation</div>
            <div class="text-sm font-semibold">₱ {{ number_format($appropriation, 2) }}</div>
        </div>
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="text-[11px] uppercase tracking-wide text-blue-500 dark:text-blue-400 mb-2">Allotment</div>
            <div class="text-sm font-semibold">₱ {{ number_format($allotment, 2) }}</div>
        </div>
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="text-[11px] uppercase tracking-wide text-primary-500 dark:text-primary-400 mb-2">Contract Amount</div>
            <div class="text-sm font-semibold">₱ {{ number_format($contractAmount, 2) }}</div>
            <div class="text-[10px] uppercase tracking-wide mb-2">{{ $contractor ?? 'No Contractor' }}</div>
        </div>
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="flex justify-between items-center mb-2">
                <span class="text-[11px] uppercase tracking-wide text-emerald-500 dark:text-emerald-400">Payments</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-500/15 dark:text-primary-300">{{ $paymentPct }}%</span>
            </div>
            <div class="text-sm font-semibold">₱ {{ number_format($totalPayment, 2) }}</div>
        </div>
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="text-[11px] uppercase tracking-wide text-red-500 dark:text-red-400 mb-2">Unpaid</div>
            <div class="text-sm font-semibold">₱ {{ number_format($unpaid, 2) }}</div>
        </div>
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="text-[11px] uppercase tracking-wide text-primary-500 dark:text-primary-400 mb-2">Implementation</div>
            <div class="text-sm font-semibold">{{ rtrim(rtrim(number_format($implPct,2), '0'), '.') }}%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Purchase Requests</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($purchaseRequests as $pr)
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="Received Date"
                            :value="$pr->received_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="PR Number"
                            :value="$pr->pr_number"

                        />
                        <x-item-badge
                            label="Remarks"
                            :value="Str::limit($pr->remarks, 20, '...')"

                            :tooltip="$pr->remarks"
                        />
                        <x-item-badge
                            label="Forwarded To TWG"
                            :value="$pr->forward_twg_date"
                            :isDay="true"

                        />
                        <x-item-badge label="Created By" :value="$pr->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Technical Working Group (TWG)</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($twgs as $twg)
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="TWG Review Date"
                            :value="$twg->review_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="TWG Review Remarks"
                            :value="Str::limit($twg->review_remarks, 20, '...')"

                            :tooltip="$twg->review_remarks"
                        />
                        <x-item-badge label="Created By" :value="$twg->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>



        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">PMO Control</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($pmoControls as $pmoControl)
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="Controlled Date"
                            :value="$pmoControl->controlled_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="ABC"
                            :value="$pmoControl->abc"

                            :isMoney="true"
                        />
                        <x-item-badge
                            label="Remarks"
                            :value="Str::limit($pmoControl->remarks, 20, '...')"

                            :tooltip="$pmoControl->remarks"
                        />
                        <x-item-badge label="Created By" :value="$pmoControl->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Purchase Request Control</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($prControls as $prc)
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="PR Controlled Date"
                            :value="$prc->controlled_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="PR Control Number"
                            :value="$prc->control_number"

                        />
                        <x-item-badge
                            label="Amount"
                            :value="$prc->amount"

                            :isMoney="true"
                        />
                        <x-item-badge label="Created By" :value="$prc->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            class="md:col-span-2"
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Procurement</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($procurements as $proc)
                    <div class="grid grid-cols-1 md:grid-cols-2 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge
                            label="IB Number"
                            :value="$proc->ib_number"
                        />
                        <x-item-badge
                            label="Pre Procument Conference"
                            :value="$proc->pre_procurement_conference"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Pre Bid Conference"
                            :value="$proc->pre_bid_conference"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Bid Opening"
                            :value="$proc->bid_opening"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="BER"
                            :value="$proc->ber"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Post Qua Date"
                            :value="$proc->post_qua_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Remarks"
                            :value="Str::limit($proc->remarks, 30, '...')"

                            :tooltip="$proc->remarks"
                        />
                        <x-item-badge
                            label="NOA Date Received"
                            :value="$proc->noa_date_received"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Contract Amount"
                            :value="$proc->contract_amount"

                            :isMoney="true"
                        />
                        <x-item-badge
                            label="Contractor"
                            :value="Str::limit($proc->contractor, 30, '...')"

                        />
                        <x-item-badge
                            label="NTP Number"
                            :value="$proc->ntp_number"

                        />
                        <x-item-badge
                            label="NTP Date"
                            :value="$proc->ntp_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Contract Duration"
                            :value="$proc->contract_duration"

                            :isDay="true"
                        />
                        <x-item-badge label="Created By" :value="$proc->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Obligation Request</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($obrs as $obr)
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="OBR Controlled Date"
                            :value="$obr->controlled_date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="OBR Number"
                            :value="$obr->number"

                        />
                        <x-item-badge
                            label="Amount"
                            :value="$obr->amount"

                            :isMoney="true"
                        />
                        <x-item-badge label="Created By" :value="$obr->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Implementation</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($implementations as $imp)
                    @if ($loop->first)
                        <x-item-badge
                            label="Start date"
                            :value="$imp->start_date"
                            :isDay="true"
                        />
                        <x-item-badge
                            label="Completion date"
                            :value="$imp->end_date"
                            :isDay="true"
                        />
                        <x-item-badge
                            label="Coordinates"
                            :value="$imp->coordinates"
                        />
                    @endif
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="Date"
                            :value="$imp->date"
                            :isDay="true"

                        />
                        <x-item-badge
                            label="Percentage"
                            :value="is_numeric($imp->percentage)
                            ? rtrim(rtrim(number_format($imp->percentage, 2, '.', ''), '0'), '.') . ' %'
                            : $imp->percentage"

                        />
                        <x-item-badge
                            label="Remarks"
                            :value="Str::limit($imp->remarks, 30, '...')"

                            :tooltip="$imp->remarks"
                        />
                        <x-item-badge label="Created By" :value="$imp->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No purchase requests found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Payments</x-slot>
            <div class="{{ $sectionBody }}">
                @forelse ($payments as $pay)
                    <div class="{{ $cardWrap }}">
                        <x-item-badge
                            label="Type of Payment"
                            :value="App\Enums\CustomOptions::PAYMENTS[$pay->type] ?? 'unknown'"

                        />
                        <x-item-badge
                            label="Date of Payment"
                            :value="$pay->date"
                            :isDay="true"
                        />
                        <x-item-badge
                            label="Amount"
                            :value="$pay->amount"
                            :isMoney="true"
                        />
                        <x-item-badge
                            label="Payable Ref."
                            :value="$pay->payable_reference"
                        />
                        <x-item-badge
                            label="Payment Ref."
                            :value="$pay->payment_reference"
                        />
                        <x-item-badge
                            label="Check Number"
                            :value="$pay->check_number"
                        />
                        <x-item-badge
                            label="Check Date"
                            :value="$pay->check_date"
                            :isDay="true"
                        />
                        <x-item-badge label="Created By" :value="$pay->user->name" :isCreator="true" />
                    </div>
                @empty
                    <div class="text-gray-500 italic">No payments found.</div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Balance</x-slot>
            <div class="{{ $sectionBody }}">
                <div class="{{ $cardWrap }}">
                    <x-item-badge label="Total Payments" :value="$totalPayment" :isMoney="true" />
                    <x-item-badge label="Unpaid Obligation" :value="$unpaid" :isMoney="true" />
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament::page>

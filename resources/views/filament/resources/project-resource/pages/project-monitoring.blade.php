<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-filament::section
            :collapsible="true"
            :collapsed="false"
        >
            <x-slot name="heading">Purchase Requests</x-slot>
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->purchase_requests as $pr)
                    <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge 
                            label="Received Date" 
                            :value="$pr->received_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="PR Number" 
                            :value="$pr->pr_number" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Forwarded To TWG" 
                            :value="$pr->forward_twg_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Created By" 
                            :value="$pr->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->technical_working_groups as $twg)
                    <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge 
                            label="TWG Review Date" 
                            :value="$twg->review_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="TWG Review Remarks" 
                            :value="Str::limit($twg->review_remarks, 20, '...')" 
                            :withIcon="true"
                            :tooltip="$twg->review_remarks"
                        />
                        <x-item-badge 
                            label="Controlled Date" 
                            :value="$twg->controlled_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="ABC" 
                            :value="$twg->abc" 
                            :withIcon="true"
                            :isMoney="true"
                        />
                        <x-item-badge 
                            label="Remarks" 
                            :value="Str::limit($twg->remarks, 20, '...')"
                            :withIcon="true"
                            :tooltip="$twg->remarks"
                        />
                        <x-item-badge 
                            label="Created By" 
                            :value="$twg->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->purchase_request_controls as $prc)
                    <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge 
                            label="PR Controlled Date" 
                            :value="$prc->controlled_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="PR Control Number" 
                            :value="$prc->control_number" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Amount" 
                            :value="$prc->amount" 
                            :withIcon="true"
                            :isMoney="true"
                        />
                        <x-item-badge 
                            label="Created By" 
                            :value="$prc->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->procurements as $proc)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge 
                            label="IB Number" 
                            :value="$proc->ib_number" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Pre Procument Conference" 
                            :value="$proc->pre_procurement_conference" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Pre Bid Conference" 
                            :value="$proc->pre_bid_conference" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Bid Opening" 
                            :value="$proc->bid_opening" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="BER" 
                            :value="$proc->ber" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Post Qua Date" 
                            :value="$proc->post_qua_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Remarks" 
                            :value="Str::limit($proc->remarks, 20, '...')" 
                            :withIcon="true"
                            :tooltip="$proc->remarks"
                        />
                        <x-item-badge 
                            label="NOA Date Received" 
                            :value="$proc->noa_date_received" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Contract Amount" 
                            :value="$proc->contract_amount" 
                            :withIcon="true"
                            :isMoney="true"
                        />
                        <x-item-badge 
                            label="Contractor" 
                            :value="$proc->contractor" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="NTP Number" 
                            :value="$proc->ntp_number" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="NTP Date" 
                            :value="$proc->ntp_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Contract Duration" 
                            :value="$proc->contract_duration" 
                            :withIcon="true"
                            :isDay="true"
                        />
                        <x-item-badge 
                            label="Created By" 
                            :value="$proc->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->obligation_requests as $obr)
                    <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge 
                            label="OBR Controlled Date" 
                            :value="$obr->controlled_date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="OBR Number" 
                            :value="$obr->number" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="Amount" 
                            :value="$obr->amount" 
                            :withIcon="true"
                            :isMoney="true"
                        />
                        <x-item-badge 
                            label="Created By" 
                            :value="$obr->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->implementations as $imp)
                    <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge
                            label=""
                            :value="$imp->date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="" 
                            :value="is_numeric($imp->percentage) 
                            ? rtrim(rtrim(number_format($imp->percentage, 2, '.', ''), '0'), '.') . ' %' 
                            : $imp->percentage" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label=""
                            :value="$imp->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @forelse ($this->project->payments as $pay)
                    <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                        <x-item-badge 
                            label="" 
                            :value="App\Enums\CustomOptions::PAYMENTS[$pay->type] ?? 'unknown'" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="" 
                            :value="$pay->date" 
                            :withIcon="true"
                        />
                        <x-item-badge 
                            label="" 
                            :value="$pay->amount" 
                            :withIcon="true"
                            :isMoney="true"
                        />
                        <x-item-badge 
                            label="" 
                            :value="$pay->user->name" 
                            :withIcon="true"
                        />
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
            <div class="text-sm text-gray-700 whitespace-normal leading-6 text-gray-950 dark:text-white space-y-4">
                @php
                    $totalPayment = $this->project->payments->sum('amount');
                    $abc = $this->project->technical_working_groups->sum('abc');
                    $unpaid = $abc - $totalPayment;
                @endphp
                <div class="space-y-1 p-4 rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm">
                    <x-item-badge 
                        label="Total Payments" 
                        :value="$totalPayment" 
                        :isMoney="true"
                    />
                    <x-item-badge 
                        label="Unpaid Obligation" 
                        :value="$unpaid" 
                        :isMoney="true"
                    />
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament::page>

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex gap-4">
            <x-filament::button
                wire:click="viewProjectsPdf"
                color="primary"
                icon="heroicon-o-eye"
            >
                View PDF
            </x-filament::button>

            <x-filament::button
                wire:click="downloadProjectsPdf"
                color="success"
                icon="heroicon-o-arrow-down-tray"
            >
                Download PDF
            </x-filament::button>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold mb-4">PDF Report Preview</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Generate comprehensive project reports including all related data:
            </p>
            <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 list-disc list-inside space-y-1">
                <li>Project Details</li>
                <li>Purchase Requests</li>
                <li>Technical Working Groups</li>
                <li>Purchase Request Controls</li>
                <li>Procurements</li>
                <li>Obligation Requests</li>
                <li>Implementations</li>
                <li>Payments</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>

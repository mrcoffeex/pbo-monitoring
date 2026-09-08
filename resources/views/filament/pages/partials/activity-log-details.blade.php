@php
    /** @var \Spatie\Activitylog\Models\Activity $activity */
    /** @var list<array{field: string, old: string, new: string}> $changes */
@endphp

<div class="space-y-6">
    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">User</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $activity->causer?->name ?? 'System' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">When</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">
                {{ $activity->created_at?->format('M d, Y g:i A') ?? '—' }}
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Event</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ \App\Filament\Pages\Activities::eventLabel($activity->event) }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Record</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ \App\Filament\Pages\Activities::subjectSummary($activity) }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Description</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $activity->description ?: '—' }}</dd>
        </div>
    </dl>

    @if ($changes === [])
        <p class="text-sm text-gray-500 dark:text-gray-400">No field changes were stored with this log.</p>
    @else
        <div class="overflow-hidden rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs font-medium text-gray-500 dark:bg-white/5 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2.5">Field</th>
                        <th class="px-4 py-2.5">Old</th>
                        <th class="px-4 py-2.5">New</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($changes as $change)
                        <tr>
                            <td class="px-4 py-2.5 font-medium capitalize text-gray-950 dark:text-white">{{ $change['field'] }}</td>
                            <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">{{ $change['old'] }}</td>
                            <td class="px-4 py-2.5 text-gray-950 dark:text-white">{{ $change['new'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

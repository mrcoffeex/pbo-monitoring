<div class="flex gap-2 w-full">
    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('monitoring', ['record' => $record]) }}"
       class="filament-button bg-zinc-600 text-white px-2 py-1 text-xs rounded hover:bg-zinc-700 transition">
        View
    </a>

    <a href="{{ route('projects.pdf.single', $record) }}"
       class="filament-button bg-pink-600 text-white px-2 py-1 text-xs rounded hover:bg-pink-700 transition inline-flex items-center gap-1"
       title="Export PDF for this project">
        PDF
    </a>
</div>

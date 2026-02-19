<div class="flex gap-2 w-full">
    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('monitoring', ['record' => $record]) }}"
       class="filament-button bg-zinc-600 text-white px-2 py-1 text-xs rounded hover:bg-zinc-700 transition uppercase">
        monitoring
    </a>

    <a href="{{ route('projects.pdf.single', $record) }}"
       class="filament-button bg-green-600 text-white px-2 py-1 text-xs rounded hover:bg-green-700 transition inline-flex items-center gap-1 uppercase"
       title="Export PDF for this project">
        pdf
    </a>
</div>

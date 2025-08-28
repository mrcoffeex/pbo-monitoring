<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_current_pdf')
                ->label('Export Current Page (PDF)')
                ->color('gray')
                ->icon('heroicon-o-eye')
                ->action('exportCurrentPagePdf'),

            Actions\Action::make('export_all_filtered_pdf')
                ->label('Export All Filtered (PDF)')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportAllFilteredPdf'),

            Actions\CreateAction::make(),
        ];
    }

    public function exportCurrentPagePdf()
    {
        try {
            // Records shown on the current page (respects filters, search, sort, and pagination)
            $ids = $this->getTableRecords()->pluck('id')->all();

            if (empty($ids)) {
                Notification::make()
                    ->warning()
                    ->title('No Records on Current Page')
                    ->body('There are no project records on the current page to export.')
                    ->send();

                return null;
            }

            return $this->generatePdfFromIds($ids, 'projects-current-page');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Export Failed')
                ->body('Failed to export current page: ' . $e->getMessage())
                ->send();

            return null;
        }
    }

    public function exportAllFilteredPdf()
    {
        try {
            // All records that match current filters/search/sort (ignores pagination)
            // Uses the table's filtered query, then refetches with relationships.
            $model = static::getModel();
            $table = (new $model())->getTable();

            $ids = $this->getFilteredTableQuery()
                ->clone()
                ->select($table . '.id')
                ->pluck($table . '.id')
                ->all();

            if (empty($ids)) {
                Notification::make()
                    ->warning()
                    ->title('No Filtered Records')
                    ->body('No project records match the current filters. Please adjust your filters or ensure there are projects available.')
                    ->send();

                return null;
            }

            return $this->generatePdfFromIds($ids, 'projects-filtered');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Export Failed')
                ->body('Failed to export filtered records: ' . $e->getMessage())
                ->send();

            return null;
        }
    }

    protected function generatePdfFromIds(array $ids, string $basename)
    {
        if (empty($ids)) {
            Notification::make()
                ->warning()
                ->title('No Records to Export')
                ->body('No project records found to export. Please check your filters or ensure there are projects available.')
                ->send();

            return null;
        }

        try {
            // Keep the same order as the table result
            $order = implode(',', $ids);

            $projects = Project::with([
                    'user',
                    'purchase_requests.user',
                    'technical_working_groups.user',
                    'purchase_request_controls.user',
                    'procurements.user',
                    'obligation_requests.user',
                    'implementations.user',
                    'payments.user',
                ])
                ->whereIn('id', $ids)
                ->when($order, fn ($q) => $q->orderByRaw("FIELD(id, $order)"))
                ->get();

            if ($projects->isEmpty()) {
                Notification::make()
                    ->warning()
                    ->title('No Projects Found')
                    ->body('The selected project records could not be found in the database.')
                    ->send();

                return null;
            }

            $html = view('pdf.projects-report', compact('projects'))->render();
            $normalized = @iconv('UTF-8', 'UTF-8//IGNORE', $html);
            if ($normalized !== false) {
                $html = $normalized;
            }

            // Long bond (8.5 x 13 inches): 612 x 936 pt. Use landscape to match your layout.
            $pdf = Pdf::setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isRemoteEnabled' => false,
                    'isHtml5ParserEnabled' => true,
                ])
                ->loadHTML(
                    // Inject @page margins if needed
                    '<style>@page{size:8.5in 13in landscape;margin:12mm;}body{margin:0;}</style>' . $html
                )
                ->setPaper([0, 0, 612, 936], 'landscape');

            Notification::make()
                ->success()
                ->title('PDF Export Successful')
                ->body("Successfully exported {$projects->count()} project records.")
                ->send();

            return response()->streamDownload(
                fn () => print($pdf->output()),
                $basename . '-' . now()->format('Y-m-d') . '.pdf',
                ['Content-Type' => 'application/pdf']
            );

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('PDF Export Failed')
                ->body('An error occurred while generating the PDF: ' . $e->getMessage())
                ->send();

            return null;
        }
    }
}

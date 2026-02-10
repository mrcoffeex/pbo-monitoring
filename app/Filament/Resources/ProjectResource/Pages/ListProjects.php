<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_current_xlsx')
                ->label('Export Current Page (Excel)')
                ->color('success')
                ->outlined()
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportCurrentPageExcel'),

            Actions\Action::make('export_all_filtered_xlsx')
                ->label('Export All Filtered (Excel)')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportAllFilteredExcel'),

            Actions\Action::make('export_current_pdf')
                ->label('Export Current Page (PDF)')
                ->color('info')
                ->outlined()
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportCurrentPagePdf'),

            Actions\Action::make('export_all_filtered_pdf')
                ->label('Export All Filtered (PDF)')
                ->color('info')
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

    public function exportCurrentPageExcel()
    {
        try {
            $records = $this->getTableRecords();

            if ($records->isEmpty()) {
                Notification::make()
                    ->warning()
                    ->title('No Records on Current Page')
                    ->body('There are no project records on the current page to export.')
                    ->send();

                return null;
            }

            // Load relationships
            $ids = $records->pluck('id')->all();
            $records = Project::with([
                'user',
                'purchase_requests.user',
                'technical_working_groups.user',
                'procurement_controls.user',
                'purchase_request_controls.user',
                'procurements.user',
                'obligation_requests.user',
                'implementations.user',
                'payments.user',
            ])
            ->whereIn('id', $ids)
            ->get();

            return $this->generateExcelFromRecords($records, 'projects-current-page');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Export Failed')
                ->body('Failed to export current page: ' . $e->getMessage())
                ->send();

            return null;
        }
    }

    public function exportAllFilteredExcel()
    {
        try {
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

            $order = implode(',', $ids);
            $records = Project::with([
                'user',
                'purchase_requests.user',
                'technical_working_groups.user',
                'procurement_controls.user',
                'purchase_request_controls.user',
                'procurements.user',
                'obligation_requests.user',
                'implementations.user',
                'payments.user',
            ])
            ->whereIn('id', $ids)
            ->when($order, fn ($q) => $q->orderByRaw("FIELD(id, $order)"))
            ->get();

            return $this->generateExcelFromRecords($records, 'projects-filtered');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Export Failed')
                ->body('Failed to export filtered records: ' . $e->getMessage())
                ->send();

            return null;

        }
    }
    protected function generateExcelFromRecords($records, string $basename)
    {
        if ($records->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('No Records to Export')
                ->body('No project records found to export. Please check your filters or ensure there are projects available.')
                ->send();

            return null;
        }

        try {
            $filename = $basename . '-' . now()->format('Y-m-d_His') . '.xlsx';
            $filepath = storage_path('app/exports/' . $filename);

            // Ensure directory exists
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            // --- Writer with options ---
            $options = new Options();

            // Column widths (1-indexed columns): tune for readability
            //  1=Res.Center  2=Project Name  3=Year  4=Appropriation  5=Allotment
            //  6=PR  7=TWG  8=PMO  9=PRC  10=Procurements
            //  11=OR  12=Implementations  13=Payments  14=Status  15=Created By  16=Created At
            $options->setColumnWidth(14, 1);    // Res. Center
            $options->setColumnWidth(36, 2);    // Project Name
            $options->setColumnWidth(8, 3);     // Year
            $options->setColumnWidth(18, 4);    // Appropriation
            $options->setColumnWidth(18, 5);    // Allotment
            $options->setColumnWidth(32, 6);    // PurchaseRequests
            $options->setColumnWidth(28, 7);    // TWG
            $options->setColumnWidth(28, 8);    // PMO Controls
            $options->setColumnWidth(28, 9);    // PRC Controls
            $options->setColumnWidth(30, 10);   // Procurements
            $options->setColumnWidth(28, 11);   // ObligationRequests
            $options->setColumnWidth(28, 12);   // Implementations
            $options->setColumnWidth(28, 13);   // Payments
            $options->setColumnWidth(14, 14);   // Status
            $options->setColumnWidth(16, 15);   // Created By
            $options->setColumnWidth(20, 16);   // Created At

            $options->DEFAULT_ROW_HEIGHT = 20;

            $writer = new XlsxWriter($options);
            $writer->openToFile($filepath);

            // Rename sheet
            $sheet = $writer->getCurrentSheet();
            $sheet->setName('Projects Report');

            // --- Styles ---
            $thinBorder = new Border(
                new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
                new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            );

            // Title style: large bold, dark background
            $titleStyle = (new Style())
                ->setFontBold()
                ->setFontSize(14)
                ->setFontColor(Color::WHITE)
                ->setBackgroundColor('1F4E79')
                ->setCellAlignment(CellAlignment::CENTER)
                ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

            // Sub-title / info row
            $infoStyle = (new Style())
                ->setFontSize(10)
                ->setFontItalic()
                ->setFontColor('555555')
                ->setCellAlignment(CellAlignment::LEFT);

            // Header row style: bold, blue bg, white text, centered, borders
            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontSize(11)
                ->setFontColor(Color::WHITE)
                ->setBackgroundColor('2F75B5')
                ->setCellAlignment(CellAlignment::CENTER)
                ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
                ->setShouldWrapText(true)
                ->setBorder($thinBorder);

            // Default data style: borders, wrap text, vertical top
            $dataStyle = (new Style())
                ->setFontSize(10)
                ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
                ->setShouldWrapText(true)
                ->setBorder($thinBorder);

            // Number style: right-aligned, borders
            $numberStyle = (new Style())
                ->setFontSize(10)
                ->setCellAlignment(CellAlignment::RIGHT)
                ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
                ->setBorder($thinBorder);

            // Status style: centered, bold
            $statusStyle = (new Style())
                ->setFontSize(10)
                ->setFontBold()
                ->setCellAlignment(CellAlignment::CENTER)
                ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
                ->setBorder($thinBorder);

            // Alternating row background
            $altDataStyle = (new Style())
                ->setFontSize(10)
                ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
                ->setShouldWrapText(true)
                ->setBorder($thinBorder)
                ->setBackgroundColor('D6E4F0');

            $altNumberStyle = (new Style())
                ->setFontSize(10)
                ->setCellAlignment(CellAlignment::RIGHT)
                ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
                ->setBorder($thinBorder)
                ->setBackgroundColor('D6E4F0');

            $altStatusStyle = (new Style())
                ->setFontSize(10)
                ->setFontBold()
                ->setCellAlignment(CellAlignment::CENTER)
                ->setCellVerticalAlignment(CellVerticalAlignment::TOP)
                ->setBorder($thinBorder)
                ->setBackgroundColor('D6E4F0');

            // --- Title row ---
            $writer->addRow(Row::fromValues(
                [env('APP_NAME') . ' — Projects Report'],
                $titleStyle
            ));

            // Merge title across all 16 columns (0-based: col 0 to col 15, row 1)
            $options->mergeCells(0, 1, 15, 1);

            // Info row: export date + record count
            $writer->addRow(Row::fromValues(
                ['Generated: ' . now()->format('F d, Y h:i A') . '  |  Records: ' . $records->count()],
                $infoStyle
            ));
            $options->mergeCells(0, 2, 15, 2);

            // Blank spacer row
            $writer->addRow(Row::fromValues([]));

            // --- Header row (row 4) ---
            $headers = [
                'Res. Center',
                'Project Name',
                'Year',
                'Appropriation',
                'Allotment',
                'Purchase Requests',
                'Technical Working Groups',
                'PMO Controls',
                'PR Controls',
                'Procurements',
                'Obligation Requests',
                'Implementations',
                'Payments',
                'Status',
                'Created By',
                'Created At',
            ];
            $writer->addRow(Row::fromValues($headers, $headerStyle));

            // --- Data rows ---
            $rowIndex = 0;
            foreach ($records as $project) {
                $isAlt = ($rowIndex % 2 === 1);
                $currentDataStyle   = $isAlt ? $altDataStyle   : $dataStyle;
                $currentNumStyle    = $isAlt ? $altNumberStyle  : $numberStyle;
                $currentStatusStyle = $isAlt ? $altStatusStyle  : $statusStyle;

                // Build cells with per-column styles
                $cells = [
                    Cell::fromValue($project->code ?? 'N/A', $currentDataStyle),
                    Cell::fromValue($project->name ?? 'N/A', $currentDataStyle),
                    Cell::fromValue($project->year ?? '', $currentDataStyle),
                    Cell::fromValue($project->appropriation ? number_format((float) $project->appropriation, 2) : '0.00', $currentNumStyle),
                    Cell::fromValue($project->allotment ? number_format((float) $project->allotment, 2) : '0.00', $currentNumStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->purchase_requests), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->technical_working_groups), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->procurement_controls), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->purchase_request_controls), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->procurements), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->obligation_requests), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->implementations), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->payments), $currentDataStyle),
                    Cell::fromValue(strtoupper($project->status ?? ''), $currentStatusStyle),
                    Cell::fromValue($project->user?->name ?? 'System', $currentDataStyle),
                    Cell::fromValue($project->created_at?->format('Y-m-d H:i:s') ?? '', $currentDataStyle),
                ];

                $writer->addRow(new Row($cells));
                $rowIndex++;
            }

            $writer->close();

            Notification::make()
                ->success()
                ->title('Excel Export Successful')
                ->body("Successfully exported {$records->count()} project records.")
                ->send();

            return response()->download($filepath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Excel Export Failed')
                ->body('An error occurred while generating the Excel file: ' . $e->getMessage())
                ->send();

            return null;
        }
    }

    private function formatRelatedRecords($records)
    {
        if (empty($records) || $records->count() === 0) {
            return 'None';
        }

        $details = [];
        foreach ($records as $record) {
            $detail = $this->getRecordDetail($record);
            if ($detail) {
                $details[] = $detail;
            }
        }

        return implode("\n", $details) ?: 'None';
    }

    private function getRecordDetail($record)
    {
        $type = class_basename($record);

        return match ($type) {
            'PurchaseRequest' => sprintf(
                'PR: %s (Received: %s)',
                $record->pr_number ?? 'N/A',
                optional($record->received_date)->format('M-d-Y') ?? 'N/A'
            ),
            'TechnicalWorkingGroup' => sprintf(
                'TWG (Reviewed: %s)',
                optional($record->review_date)->format('M-d-Y') ?? 'N/A'
            ),
            'ProcurementControl' => sprintf(
                'PMO: ABC %s (Controlled: %s)',
                number_format($record->abc ?? 0, 2),
                optional($record->controlled_date)->format('M-d-Y') ?? 'N/A'
            ),
            'PurchaseRequestControl' => sprintf(
                'PRC: %s (%s)',
                $record->control_number ?? 'N/A',
                number_format($record->amount ?? 0, 2)
            ),
            'Procurement' => sprintf(
                'IB: %s (NTP: %s)',
                $record->ib_number ?? 'N/A',
                $record->ntp_number ?? 'Pending'
            ),
            'ObligationRequest' => sprintf(
                'OR: %s (%s)',
                $record->or_number ?? 'N/A',
                optional($record->or_date)->format('M-d-Y') ?? 'N/A'
            ),
            'Implementation' => sprintf(
                'Impl: %s%% (as of %s)',
                $record->percentage ?? 0,
                optional($record->date)->format('M-d-Y') ?? 'N/A'
            ),
            'Payment' => sprintf(
                'Payment: %s (Ref: %s)',
                number_format($record->amount ?? 0, 2),
                $record->reference_number ?? 'N/A'
            ),
            default => 'N/A'
        };
    }
}

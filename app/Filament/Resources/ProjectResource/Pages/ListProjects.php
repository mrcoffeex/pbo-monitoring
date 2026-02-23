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
                    'pre_procurements.user',
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
                'pre_procurements.user',
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
                'pre_procurements.user',
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
            //  1=Res.Center  2=Appropriation  3=Allotment  4=PreProcurements
            //  5=PR  6=TWG  7=PMO  8=PRC  9=Procurements
            //  10=OR  11=Implementations  12=Payments
            $options->setColumnWidth(18, 1);    // Res. Center
            $options->setColumnWidth(18, 2);    // Appropriation
            $options->setColumnWidth(18, 3);    // Allotment
            $options->setColumnWidth(32, 4);    // PreProcurements
            $options->setColumnWidth(32, 5);    // PurchaseRequests
            $options->setColumnWidth(32, 6);    // TWG
            $options->setColumnWidth(32, 7);    // PMO Controls
            $options->setColumnWidth(32, 8);    // PRC Controls
            $options->setColumnWidth(40, 9);    // Procurements
            $options->setColumnWidth(32, 10);   // ObligationRequests
            $options->setColumnWidth(40, 11);   // Implementations
            $options->setColumnWidth(40, 12);   // Payments

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

            // Merge title across all 12 columns (0-based: col 0 to col 11, row 1)
            $options->mergeCells(0, 1, 11, 1);

            // Info row: export date + record count
            $writer->addRow(Row::fromValues(
                ['Generated: ' . now()->format('F d, Y h:i A') . '  |  Records: ' . $records->count()],
                $infoStyle
            ));
            $options->mergeCells(0, 2, 11, 2);

            // Blank spacer row
            $writer->addRow(Row::fromValues([]));

            // --- Header row (row 4) ---
            $headers = [
                'Res. Center',
                'Appropriation',
                'Allotment',
                'Pre Procurements',
                'Purchase Requests',
                'Technical Working Groups',
                'PMO Controls',
                'PR Controls',
                'Procurements',
                'Obligation Requests',
                'Implementations',
                'Payments',
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
                $centerLabel = ($project->code ?? 'N/A') . ' - ' . ($project->name ?? '');
                $cells = [
                    Cell::fromValue($centerLabel, $currentDataStyle),
                    Cell::fromValue($project->appropriation ? number_format((float) $project->appropriation, 2) : '0.00', $currentNumStyle),
                    Cell::fromValue($project->allotment ? number_format((float) $project->allotment, 2) : '0.00', $currentNumStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->pre_procurements), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->purchase_requests), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->technical_working_groups), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->procurement_controls), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->purchase_request_controls), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->procurements), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->obligation_requests), $currentDataStyle),
                    Cell::fromValue($this->formatRelatedRecords($project->implementations), $currentDataStyle),
                    Cell::fromValue($this->formatPayments($project), $currentDataStyle),
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

    private function formatPayments($project)
    {
        $result = [];
        $result[] = 'Balance: ' . number_format((float)($project->balance ?? 0), 2);

        if ($project->payments && $project->payments->count() > 0) {
            foreach ($project->payments as $payment) {
                $paymentType = \App\Enums\CustomOptions::PAYMENTS[$payment->type] ?? $payment->type;
                $result[] = sprintf(
                    'Payment: %s (%s)',
                    number_format($payment->amount ?? 0, 2),
                    optional($payment->date)->format('M-d-Y') ?? 'N/A'
                );
            }
        }

        return implode("\n", $result);
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
            'PreProcurement' => sprintf(
                'Remarks: %s | Date: %s',
                $record->remarks ?? 'N/A',
                optional($record->created_at)->format('M-d-Y') ?? 'N/A'
            ),
            'PurchaseRequest' => sprintf(
                'PR: %s | Received: %s | Remarks: %s | Forwarded to TWG: %s',
                $record->pr_number ?? 'N/A',
                optional($record->received_date)->format('M-d-Y') ?? 'N/A',
                $record->remarks ?? 'N/A',
                optional($record->forward_twg_date)->format('M-d-Y') ?? 'N/A'
            ),
            'TechnicalWorkingGroup' => sprintf(
                'Review Date: %s | Remarks: %s',
                optional($record->review_date)->format('M-d-Y') ?? 'N/A',
                $record->review_remarks ?? 'N/A'
            ),
            'ProcurementControl' => sprintf(
                'Controlled: %s | ABC: %s | Remarks: %s',
                optional($record->controlled_date)->format('M-d-Y') ?? 'N/A',
                number_format($record->abc ?? 0, 2),
                $record->forward_twg_date ?? 'N/A'
            ),
            'PurchaseRequestControl' => sprintf(
                'Controlled: %s | Control#: %s | Amount: %s',
                optional($record->controlled_date)->format('M-d-Y') ?? 'N/A',
                $record->control_number ?? 'N/A',
                number_format($record->amount ?? 0, 2)
            ),
            'Procurement' => sprintf(
                'IB: %s | Pre-Proc Conf: %s | Pre-Bid Conf: %s | NTP: %s | Contract: %s',
                $record->ib_number ?? 'N/A',
                optional($record->pre_procurement_conference)->format('M-d-Y') ?? 'N/A',
                optional($record->pre_bid_conference)->format('M-d-Y') ?? 'N/A',
                $record->ntp_number ?? 'Pending',
                number_format($record->contract_amount ?? 0, 2)
            ),
            'ObligationRequest' => sprintf(
                'OR#: %s | Controlled: %s | Amount: %s',
                $record->number ?? 'N/A',
                optional($record->controlled_date)->format('M-d-Y') ?? 'N/A',
                number_format($record->amount ?? 0, 2)
            ),
            'Implementation' => sprintf(
                'Date: %s | Percentage: %s%% | Remarks: %s',
                optional($record->date)->format('M-d-Y') ?? 'N/A',
                $record->percentage ?? 0,
                $record->remarks ?? 'N/A'
            ),
            default => 'N/A'
        };
    }
}

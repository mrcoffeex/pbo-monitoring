<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectPdfController extends Controller
{
    public function download()
    {
        $projects = Project::with([
            'center','user',
            'purchase_requests.user',
            'technical_working_groups.user',
            'purchase_request_controls.user',
            'procurements.user',
            'obligation_requests.user',
            'implementations.user',
            'payments.user',
        ])->get();

        $html = view('pdf.projects-report', compact('projects'))->render();
        $normalized = @iconv('UTF-8', 'UTF-8//IGNORE', $html);
        if ($normalized !== false) {
            $html = $normalized;
        }

        $pdf = Pdf::setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])
            ->loadHTML($html)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'projects-report-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}

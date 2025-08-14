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
    public function single(Project $project)
    {
        $project->load([
            'center','user',
            'purchase_requests.user',
            'technical_working_groups.user',
            'purchase_request_controls.user',
            'procurements.user',
            'obligation_requests.user',
            'implementations.user',
            'payments.user',
        ]);

        $projects = collect([$project]); // reuse the same PDF view expecting a $projects collection

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
            ->loadHTML('<style>@page{size:8.5in 13in landscape;margin:12mm;}body{margin:0;}</style>'.$html)
            ->setPaper([0,0,612,936], 'landscape'); // 8.5 x 13 in

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'project-'.$project->id.'-'.now()->format('Y-m-d').'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}

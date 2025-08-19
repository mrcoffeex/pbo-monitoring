<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Pages\Page;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ProjectPdfViewer extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.project-pdf-viewer';
    protected static ?string $title = 'PDF Reports';

    public function viewProjectsPdf()
    {
        $projects = Project::with([
            'user',
            'purchase_requests.user',
            'technical_working_groups.user',
            'purchase_request_controls.user',
            'procurements.user',
            'obligation_requests.user',
            'implementations.user',
            'payments.user',
        ])->get();

        $html = view('pdf.projects-report', compact('projects'))->render();
        $html = iconv('UTF-8', 'UTF-8//IGNORE', $html);

        return Pdf::setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])
            ->loadHTML($html)
            ->setPaper('a3', 'landscape')
            ->stream('projects-report-'.now()->format('Y-m-d').'.pdf');
    }

    public function downloadProjectsPdf()
    {
        $projects = Project::with([
            'user',
            'purchase_requests.user',
            'technical_working_groups.user',
            'purchase_request_controls.user',
            'procurements.user',
            'obligation_requests.user',
            'implementations.user',
            'payments.user',
        ])->get();

        $html = view('pdf.projects-report', compact('projects'))->render();
        $html = iconv('UTF-8', 'UTF-8//IGNORE', $html);

        $pdf = Pdf::setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])
            ->loadHTML($html)
            ->setPaper('a3', 'landscape');

        return response()->streamDownload(fn () => print($pdf->output()), 'projects-report-'.now()->format('Y-m-d').'.pdf');
    }
}

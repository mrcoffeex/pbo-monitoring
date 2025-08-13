<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #374151;
            background: white;
            margin-bottom: 24mm; /* match footer height + spacing */
        }

        @page {
            size: A4 landscape;
            margin: 15mm 12mm 22mm 12mm;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #ec4899;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #6b7280;
        }

        .date-generated {
            text-align: right;
            margin-bottom: 15px;
            font-size: 11px;
            color: #9ca3af;
        }

        .projects-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        .projects-table th {
            background: #ec4899;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        .projects-table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            word-wrap: break-word;
            max-width: 120px;
        }

        .projects-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .amount {
            font-weight: 600;
            color: #059669;
        }

        .date-text {
            font-size: 9px;
            color: #6b7280;
        }

        .related-items {
            font-size: 8px;
            color: #4b5563;
            max-height: 60px;
            overflow: hidden;
        }

        .related-items ul {
            margin: 0;
            padding-left: 12px;
        }

        .related-items li {
            margin: 1px 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #ec4899;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .summary-card h3 {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card .value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding: 10px;
            background: white;
            height: 16mm; /* approximate height for spacing */
        }
    </style>
</head>
<body>
    @php
        $sanitize = function ($v): string {
            if ($v === null) return '';
            if (! is_string($v)) $v = (string) $v;

            // Replace NBSP and remove zero-width/byte order marks
            $v = preg_replace('/\x{00A0}/u', ' ', $v) ?? $v;                   // NBSP → space
            $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $v) ?? $v; // zero-width chars

            if (function_exists('mb_check_encoding') && ! mb_check_encoding($v, 'UTF-8')) {
                $v = @mb_convert_encoding($v, 'UTF-8', 'auto, ISO-8859-1, Windows-1252') ?: $v;
            }

            $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $v);
            return $converted !== false ? $converted : $v;
        };

        $money = function ($n): string {
            $n = is_numeric($n) ? (float) $n : 0;
            return 'PHP ' . number_format($n, 2);
        };

        $date = function ($d, string $fmt = 'M d, Y'): string {
            if (empty($d)) return 'N/A';
            try { return \Illuminate\Support\Carbon::parse($d)->format($fmt); }
            catch (\Throwable) { return 'N/A'; }
        };
    @endphp

    <div class="header">
        <h1>Infrastructure Projects Comprehensive Report</h1>
        <p>Complete overview of all projects with related data</p>
    </div>

    <div class="section-title">Projects Details</div>
    <table class="projects-table">
        <thead>
            <tr>
                <th style="width: 7%;">Res. Center</th>
                <th style="width: 11%;">Project</th>
                <th style="width: 8%;">Appropriation</th>
                <th style="width: 8%;">Allotment</th>
                <th style="width: 10%;">Purchase Requests</th>
                <th style="width: 10%;">Technical Working Groups</th>
                <th style="width: 9%;">PR Controls</th>
                <th style="width: 10%;">Procurements</th>
                <th style="width: 9%;">Obligation Requests</th>
                <th style="width: 9%;">Implementations</th>
                <th style="width: 9%;">Payments</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td>
                    <div style="font-weight: 600; margin-bottom: 2px;">
                        {{ $sanitize($project->center->code ?? 'N/A') }}
                    </div>
                </td>
                <td>
                    <div style="font-weight: 600; margin-bottom: 2px;">
                        {{ $sanitize($project->center->name ?? 'N/A') }}
                    </div>
                </td>
                <td><span class="amount">{{ $money($project->appropriation ?? 0) }}</span></td>
                <td><span class="amount">{{ $money($project->allotment ?? 0) }}</span></td>
                <td>
                    <div class="related-items">
                        @if($project->purchase_requests && $project->purchase_requests->count() > 0)
                            <ul>
                                @foreach($project->purchase_requests as $pr)
                                    <li>
                                        <span class="date-text">{{ $date($pr->received_date) }}</span><br>
                                        <strong>{{ $sanitize($pr->pr_number ?? 'N/A') }}</strong><br>
                                        <span class="date-text">{{ $date($pr->forward_twg_date) }}</span><br>
                                        <span class="date-text">{{ $sanitize(optional($pr->user)->name ?? 'N/A') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #9ca3af;">No purchase requests</span>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="related-items">
                        @if($project->technical_working_groups->count() > 0)
                            <ul>
                                @foreach($project->technical_working_groups as $twg)
                                    <li>
                                        <span class="date-text">{{ $date($twg->review_date) }}</span><br>
                                        <span style="font-size: 7px;">{{ $sanitize($twg->review_remarks ?? '') }}</span><br>
                                        <span class="date-text">{{ $date($twg->controlled_date) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #9ca3af;">No TWGs</span>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="related-items">
                        @if($project->purchase_request_controls->count() > 0)
                            <ul>
                                @foreach($project->purchase_request_controls as $prc)
                                    <li>
                                        <span class="date-text">{{ $date($prc->controlled_date) }}</span><br>
                                        <span style="font-size: 7px;">{{ $sanitize($prc->control_number ?? '') }}</span><br>
                                        <span class="date-text">{{ number_format((float) $prc->amount, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #9ca3af;">No PR controls</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Projects Report - Generated by {{ $sanitize(config('app.name', 'PBO Monitoring')) }} - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>

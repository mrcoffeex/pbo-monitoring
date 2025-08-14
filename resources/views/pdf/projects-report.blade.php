<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects Report</title>
    <style>
        @page { size: 8.5in 13in landscape; margin: 14mm 12mm 18mm 12mm; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: Arial, Helvetica, sans-serif; font-size:11px; color:#1f2933; line-height:1.35; }
        h1 { font-size:18px; margin:0 0 2px 0; font-weight:600; letter-spacing:.5px; }
        .subtitle { font-size:11px; color:#555; margin-bottom:10px; }
        .header-bar { padding-bottom:6px; margin-bottom:12px; border-bottom:2px solid #d0d5da; }
        .meta { text-align:right; font-size:9px; color:#555; margin-bottom:8px; }
        table { width:100%; border-collapse:collapse; table-layout:fixed; }
        th,td { border:1px solid #d9e0e6; padding:4px 5px; vertical-align:top; word-break:break-word; }
        th { background:#f3f5f7; font-weight:600; font-size:9px; text-transform:uppercase; letter-spacing:.5px; color:#374151; }
        tbody tr:nth-child(even){ background:#fafbfc; }
        .amount { font-weight:600; color:#0b735a; font-size:10px; }
        .muted { color:#9aa2aa; font-style:italic; font-size:8px; }
        .value { color: #276CF5!important; }
        .small { font-size:8px; }
        .inner-table { width:100%; border-collapse:collapse; table-layout:fixed; }
        .inner-table th, .inner-table td { border:0; padding:1px 2px; font-size:7px; line-height:1.15; }
        .inner-table thead th { background:#eef1f3; font-weight:600; font-size:7px; text-transform:none; letter-spacing:0; }
        .inner-wrap { max-height:82px; overflow:hidden; }
        .center { text-align:center; }
        .right { text-align:right; }
        .col-center{width:7%}.col-project{width:11%}.col-app{width:8%}.col-allot{width:8%}.col-pr{width:10%}.col-twg{width:10%}.col-prc{width:9%}.col-proc{width:10%}.col-obr{width:9%}.col-impl{width:9%}.col-pay{width:9%}
        .status-badge { display:inline-block; padding:1px 4px; background:#e5e7eb; border-radius:3px; font-size:7px; font-weight:600; }
        .footer { position:fixed; left:0; right:0; bottom:0; text-align:center; font-size:9px; color:#6b7279; border-top:1px solid #d0d5da; padding:4px 0 2px; }
    </style>
</head>
<body>
@php
    $sanitize = function ($v): string {
        if ($v === null) return '';
        if (!is_string($v)) $v = (string)$v;
        $v = preg_replace('/\x{00A0}/u',' ',$v) ?? $v;
        $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u','',$v) ?? $v;
        if (function_exists('mb_check_encoding') && !mb_check_encoding($v,'UTF-8')) {
            $v = @mb_convert_encoding($v,'UTF-8','auto, ISO-8859-1, Windows-1252') ?: $v;
        }
        $c = @iconv('UTF-8','UTF-8//IGNORE',$v);
        return $c !== false ? $c : $v;
    };
    $money = fn($n) => number_format(is_numeric($n)? (float)$n : 0, 2);
    $date = function ($d, $fmt='M-d-Y') {
        if (empty($d)) return '';
        try { return \Illuminate\Support\Carbon::parse($d)->format($fmt); } catch (\Throwable) { return ''; }
    };
@endphp

<div class="header-bar">
    <h1>Infrastructure Projects Report</h1>
    <div class="subtitle">Compiled listing with related transactional data (field headers reflect model attributes)</div>
    <div class="meta">Generated: {{ now()->format('Y-m-d H:i') }}</div>
</div>

<table>
    <thead>
    <tr>
        <th class="col-center">Center</th>
        <th class="col-app">Appropriation</th>
        <th class="col-allot">Allotment</th>
        <th class="col-pr">PurchaseRequests</th>
        <th class="col-twg">TechnicalWorkingGroups</th>
        <th class="col-prc">PurchaseRequestControls</th>
        <th class="col-proc">Procurements</th>
        <th class="col-obr">ObligationRequests</th>
        <th class="col-impl">Implementations</th>
        <th class="col-pay">Payments</th>
    </tr>
    </thead>
    <tbody>
    @foreach($projects as $project)
        <tr>
            <!-- Center -->
            <td>
                <div class="small" style="font-weight:600">{{ $sanitize($project->center->code ?? 'N/A') }}</div>
                <div class="small">{{ $sanitize($project->center->name ?? '') }}</div>
            </td>

            <!-- Appropriation -->
            <td class="right">
                <div class="amount">{{ $money($project->appropriation ?? 0) }}</div>
            </td>

            <!-- Allotment -->
            <td class="right">
                <div class="amount">{{ $money($project->allotment ?? 0) }}</div>
            </td>

            <!-- Purchase Requests (horizontal) -->
            <td class="small">
                @if($project->purchase_requests?->count())
                    @foreach($project->purchase_requests as $pr)
                        <li>
                            <span>
                                Received Date: <span class="value">{{ $date($pr->received_date) }}</span>
                            </span><br>
                            <strong>
                                PR No.: <span class="value">{{ $sanitize($pr->pr_number) }}</span>
                            </strong><br>
                            <span>
                                Forwarded to TWG: <span class="value">{{ $date($pr->forward_twg_date) }}</span>
                            </span><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>

            <!-- TWGs horizontal -->
            <td class="small">
                @if($project->technical_working_groups?->count())
                    @foreach($project->technical_working_groups as $twg)
                        <li>
                            <span>
                                Review Date: <span class="value">{{ $date($twg->review_date) }}</span>
                            </span><br>
                            <span>
                                Review Remarks: <span class="value">{{ $sanitize($twg->review_remarks) }}</span>
                            </span><br>
                            <span>
                                Controlled Date: <span class="value">{{ $date($twg->controlled_date) }}</span>
                            </span><br>
                            <strong>
                                ABC: <span class="value">{{ $money($twg->abc) }}</span>
                            </strong><br>
                            <span>
                                Remarks: <span class="value">{{ $sanitize($twg->forward_twg_date) }}</span>
                            </span><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>

            <!-- PR Controls horizontal -->
            <td class="small">
                @if($project->purchase_request_controls?->count())
                    @foreach($project->purchase_request_controls as $prc)
                        <li>
                            <span>
                                Controlled Date: <span class="value">{{ $date($prc->controlled_date) }}</span>
                            </span><br>
                            <span>
                                Control Number: <span class="value">{{ $sanitize($prc->control_number) }}</span>
                            </span><br>
                            <strong>
                                Amount: <span class="value">{{ $money($prc->amount) }}</span>
                            </strong><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>

            <!-- Procurements horizontal -->
            <td class="small">
                @if($project->procurements?->count())
                    @foreach($project->procurements as $proc)
                        <li>
                            <span>
                                IB No.: <span class="value">{{ $sanitize($proc->ib_number) }}</span>
                            </span><br>
                            <span>
                                Pre-Proc. Conf.: <span class="value">{{ $date($proc->pre_procurement_conference) }}</span>
                            </span><br>
                            <span>
                                Pre-Bid Conf.: <span class="value">{{ $date($proc->pre_bid_conference) }}</span>
                            </span><br>
                            <span>
                                Bid Opening: <span class="value">{{ $date($proc->bid_opening) }}</span>
                            </span><br>
                            <span>
                                BER: <span class="value">{{ $date($proc->ber) }}</span>
                            </span><br>
                            <span>
                                Post Qua.: <span class="value">{{ $date($proc->post_qua_date) }}</span>
                            </span><br>
                            <span>
                                Remarks: <span class="value">{{ $sanitize($proc->remarks) }}</span>
                            </span><br>
                            <span>
                                NOA: <span class="value">{{ $date($proc->noa_date_received) }}</span>
                            </span><br>
                            <strong>
                                Contract Amount: <span class="value">{{ $money($proc->contract_amount) }}</span>
                            </strong><br>
                            <span>
                                Contractor: <span class="value">{{ $sanitize($proc->contractor) }}</span>
                            </span><br>
                            <span>
                                NTP No.: <span class="value">{{ $sanitize($proc->ntp_number) }}</span>
                            </span><br>
                            <span>
                                NTP Date: <span class="value">{{ $date($proc->ntp_date) }}</span>
                            </span><br>
                            <span>
                                Contract Duration: <span class="value">{{ $sanitize($proc->contract_duration) . ' days' }}</span>
                            </span><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>

            <!-- Obligation Requests horizontal -->
            <td class="small">
                @if($project->obligation_requests?->count())
                    @foreach($project->obligation_requests as $obr)
                        <li>
                            <span>
                                Controlled Date: <span class="value">{{ $date($obr->controlled_date) }}</span>
                            </span><br>
                            <span>
                                Control Number: <span class="value">{{ $sanitize($obr->number) }}</span>
                            </span><br>
                            <strong>
                                Amount: <span class="value">{{ $money($obr->amount) }}</span>
                            </strong><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>

            <!-- Implementations horizontal -->
            <td class="small">
                @if($project->implementations?->count())
                    @foreach($project->implementations as $prc)
                        <li>
                            <span>
                                Date: <span class="value">{{ $date($prc->date) }}</span>
                            </span><br>
                            <strong>
                                Percentage: <span class="value">{{ $sanitize($prc->percentage) . ' %' }}</span>
                            </strong><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>

            <!-- Payments horizontal -->
            <td class="small">
                @if($project->payments?->count())
                    @foreach($project->payments as $pay)
                        <li>
                            @php
                                $paymentType = \App\Enums\CustomOptions::PAYMENTS[$pay->type] ?? $pay->type;
                            @endphp
                            <span>
                                Type: <span class="value">{{ $sanitize($paymentType) }}</span>
                            </span><br>
                            <span>
                                Date: <span class="value">{{ $date($pay->date) }}</span>
                            </span><br>
                            <strong>
                                Amount: <span class="value">{{ $money($pay->amount) }}</span>
                            </strong><br>
                        </li>
                    @endforeach
                @else
                    <span class="muted">None</span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    Projects Report • {{ $sanitize(config('app.name','PBO Monitoring')) }} • {{ now()->format('Y') }}
</div>
</body>

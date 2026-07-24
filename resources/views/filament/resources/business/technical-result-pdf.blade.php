<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Technical Result — {{ $business?->business_code ?? '' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f262a;
            background: #ffffff;
            padding: 20px 24px;
        }
        h1 { font-size: 16px; color: #db4a2b; font-weight: 700; }
        h2 { font-size: 12px; color: #1f262a; font-weight: 700; margin-bottom: 6px; }
        h3 { font-size: 11px; color: #db4a2b; font-weight: 600; margin: 10px 0 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { text-align: left; padding: 3px 5px; border-bottom: 1px solid #1f262a; font-weight: 600; color: #6b7280; }
        td { padding: 3px 5px; border-bottom: 1px solid #d1cec9; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .accent { color: #db4a2b; }
        .muted { color: #6b7280; }
        .doc-header {
            background-color: #1f262a;
            color: #f1efea;
            padding: 6px 10px;
            font-weight: 700;
            font-size: 11px;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        .section-box {
            border: 1px solid #d1cec9;
            padding: 8px 10px;
            margin-bottom: 8px;
        }
        .totals-box {
            background-color: #1f262a;
            color: #f1efea;
            padding: 10px 14px;
            margin-top: 14px;
        }
        .totals-box td { border-bottom: 1px solid #374151; color: #d1d5db; }
        .totals-box .net td { border-top: 2px solid #4b5563; color: #f1efea; font-weight: 700; }
        .badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

{{-- ══ HEADER ══════════════════════════════════════════════════════════════ --}}
<table style="margin-bottom:14px;">
    <tr>
        <td style="border:none;">
            <h1>Technical Result</h1>
            <span class="muted" style="font-size:9px;">Generated: {{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i') }}</span>
        </td>
        <td style="text-align:right; vertical-align:top; border:none;">
            <span style="font-size:9px; color:#6b7280;">RMS-System</span>
        </td>
    </tr>
</table>

{{-- ══ BUSINESS INFO ═══════════════════════════════════════════════════════ --}}
@if ($business)
<div class="section-box">
    <table>
        <tr>
            <td style="width:20%; border:none;" class="bold">Business Code:</td>
            <td style="width:30%; border:none;">{{ $business->business_code }}</td>
            <td style="width:20%; border:none;" class="bold">Reinsurer:</td>
            <td style="width:30%; border:none;">{{ $business->reinsurer?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="bold">Description:</td>
            <td colspan="3" style="border:none;">{{ $business->description ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="bold">Reinsurance Type:</td>
            <td style="border:none;">{{ $business->reinsurance_type ?? '—' }}</td>
            <td style="border:none;" class="bold">Premium Type:</td>
            <td style="border:none;">{{ $business->premium_type ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="bold">Currency:</td>
            <td style="border:none;">{{ $business->currency?->acronym ?? '—' }}</td>
            <td style="border:none;" class="bold">Documents:</td>
            <td style="border:none;">{{ count($summaries) }}</td>
        </tr>
    </table>
</div>
@endif

{{-- ══ PER-DOC SUMMARIES ════════════════════════════════════════════════════ --}}
@foreach ($summaries as $sIdx => $summary)
@php
    $docId     = $summary['id']             ?? '—';
    $docType   = $summary['documentType']   ?? '—';
    $inception = $summary['inceptionDate']  ?? null;
    $expiry    = $summary['expirationDate'] ?? null;
    $currency  = $summary['originalCurrency'] ?? '—';
    $fts       = (float) ($summary['totalPremiumFts']        ?? 0);
    $convPrem  = (float) ($summary['totalConvertedPremium']  ?? 0);
    $groupedNodes = collect($summary['groupedCostNodes'] ?? []);
    $nodesFlat    = $groupedNodes->flatMap(fn($g) => $g['nodes'] ?? [])->sortBy('index')->values();
    $grandDeductOrig = $groupedNodes->sum('subtotal_orig');
    $grandDeductUsd  = $groupedNodes->sum('subtotal_usd');
    $transactions = collect($summary['transactions'] ?? []);
    $logsByTxn    = collect($summary['logsByTxn']    ?? []);
    $netOrig = $fts - $grandDeductOrig;
    $netUsd  = $convPrem - $grandDeductUsd;
@endphp

<div class="doc-header">
    {{ sprintf('%02d', $sIdx + 1) }}. {{ $docId }} — {{ $docType }}
    @if ($inception && $expiry)
        <span style="font-weight:400; color:#9ca3af; font-size:9px; margin-left:8px;">
            {{ \Carbon\Carbon::parse($inception)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($expiry)->format('d/m/Y') }}
        </span>
    @endif
</div>

{{-- Financial Summary --}}
<table style="margin-bottom:8px;">
    <thead>
        <tr>
            <th style="width:60%;"></th>
            <th class="right">Orig. Curr. ({{ $currency }})</th>
            <th class="right">US Dollars</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="bold">Gross Underwritten Premium</td>
            <td class="right">${{ number_format($fts, 2) }}</td>
            <td class="right">${{ number_format($convPrem, 2) }}</td>
        </tr>
        <tr>
            <td class="bold">Total Deductions</td>
            <td class="right" style="color:#dc2626;">-${{ number_format($grandDeductOrig, 2) }}</td>
            <td class="right" style="color:#dc2626;">-${{ number_format($grandDeductUsd, 2) }}</td>
        </tr>
        <tr style="border-top:2px solid #1f262a;">
            <td class="bold" style="font-size:11px;">Net Underwritten Premium</td>
            <td class="right bold" style="font-size:11px;">${{ number_format($netOrig, 2) }}</td>
            <td class="right bold" style="font-size:11px;">${{ number_format($netUsd, 2) }}</td>
        </tr>
    </tbody>
</table>

{{-- Deductions detail --}}
@if ($groupedNodes->isNotEmpty())
<h3>Costs Breakdown</h3>
<table style="margin-bottom:8px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Partner</th>
            <th>Concept</th>
            <th class="right">Rate</th>
            <th class="right">Orig. Curr.</th>
            <th class="right">USD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedNodes as $group)
            @foreach ($group['nodes'] as $node)
            <tr>
                <td>{{ $node['index'] }}</td>
                <td>{{ $node['partner'] ?? '—' }}</td>
                <td>{{ $node['deduction'] ?? '—' }}</td>
                <td class="right">{{ number_format($node['value'] * 100, 2) }}%</td>
                <td class="right" style="color:#dc2626;">-${{ number_format($node['deduction_amount'], 2) }}</td>
                <td class="right" style="color:#dc2626;">-${{ number_format($node['deduction_usd'], 2) }}</td>
            </tr>
            @endforeach
            <tr style="background:#f1efea;">
                <td colspan="4" class="right muted" style="font-size:9px;">Share {{ number_format($group['share'] * 100, 2) }}% subtotal:</td>
                <td class="right bold">-${{ number_format($group['subtotal_orig'], 2) }}</td>
                <td class="right bold">-${{ number_format($group['subtotal_usd'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Installments --}}
@if ($transactions->isNotEmpty())
<h3>Installments</h3>
<table style="margin-bottom:8px;">
    <thead>
        <tr>
            <th>#</th>
            <th class="right">Proportion</th>
            <th class="right">Exch. Rate</th>
            <th class="center">Due Date</th>
            <th class="right">Orig. Curr.</th>
            <th class="right">USD</th>
        </tr>
    </thead>
    <tbody>
        @php $gOrig = 0; $gUsd = 0; @endphp
        @foreach ($transactions as $txn)
        @php
            $prop    = (float) ($txn['proportion'] ?? 0);
            $rate    = (float) ($txn['exch_rate']  ?? 0);
            $aOrig   = $netOrig * $prop;
            $aUsd    = $rate > 0 ? $aOrig / $rate : 0;
            $gOrig  += $aOrig;
            $gUsd   += $aUsd;
        @endphp
        <tr>
            <td>{{ $txn['index'] ?? $loop->iteration }}</td>
            <td class="right">{{ number_format($prop * 100, 2) }}%</td>
            <td class="right">{{ number_format($rate, 4) }}</td>
            <td class="center">{{ isset($txn['due_date']) ? \Carbon\Carbon::parse($txn['due_date'])->format('d/m/Y') : '—' }}</td>
            <td class="right">${{ number_format($aOrig, 2) }}</td>
            <td class="right">${{ number_format($aUsd, 2) }}</td>
        </tr>
        @endforeach
        <tr style="border-top:2px solid #1f262a; background:#f1efea;">
            <td colspan="4" class="right bold">Total:</td>
            <td class="right bold">${{ number_format($gOrig, 2) }}</td>
            <td class="right bold">${{ number_format($gUsd, 2) }}</td>
        </tr>
    </tbody>
</table>

{{-- Transactions Lifecycle --}}
@if ($nodesFlat->isNotEmpty())
<h3>Transactions Lifecycle</h3>
@foreach ($transactions as $tIdx => $txn)
@php
    $txnId   = $txn['id'] ?? null;
    $pRaw    = (float) ($txn['proportion'] ?? 0);
    $pPct    = ($pRaw > 1 ? $pRaw : $pRaw * 100);
    $txnLogs = $txnId ? collect($logsByTxn[$txnId] ?? []) : collect();
@endphp
<div style="margin-top:6px; margin-bottom:4px; font-weight:700; font-size:10px; background:#e8e6e1; padding:3px 6px;">
    Installment {{ $txn['index'] ?? ($tIdx + 1) }}
    <span style="font-weight:400; color:#6b7280; font-size:9px;">
        — {{ number_format($pPct, 2) }}% · Rate: {{ number_format((float)($txn['exch_rate'] ?? 0), 4) }}
        · Due: {{ isset($txn['due_date']) ? \Carbon\Carbon::parse($txn['due_date'])->format('d/m/Y') : '—' }}
    </span>
</div>
<table style="margin-bottom:6px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Concept</th>
            <th>From</th>
            <th>To</th>
            <th class="right">Gross</th>
            <th class="right">Discount</th>
            <th class="right">Banking</th>
            <th class="right">Net</th>
            <th class="center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($nodesFlat as $nIdx => $node)
        @php
            $nodeIndex = (int) ($node['index'] ?? ($nIdx + 1));
            $logRow    = $txnId ? ($txnLogs[$nodeIndex] ?? null) : null;
            $status    = $logRow['status'] ?? 'preview';
        @endphp
        <tr>
            <td>{{ ($tIdx+1) }}.{{ ($nIdx+1) }}</td>
            <td>{{ $node['deduction'] ?? '—' }}</td>
            <td>{{ $node['partner_short'] ?? $node['partner'] ?? '—' }}</td>
            <td>{{ $logRow['to_short'] ?? ($node['partner_short'] ?? '—') }}</td>
            <td class="right">{{ $logRow['gross'] !== null ? '$'.number_format((float)$logRow['gross'],2) : '—' }}</td>
            <td class="right">{{ $logRow['discount'] !== null ? '$'.number_format((float)$logRow['discount'],2) : '—' }}</td>
            <td class="right">{{ $logRow['banking'] !== null ? '$'.number_format((float)$logRow['banking'],2) : '—' }}</td>
            <td class="right bold">{{ $logRow['net'] !== null ? '$'.number_format((float)$logRow['net'],2) : '—' }}</td>
            <td class="center">{{ $status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endif
@endif

@if (!$loop->last)
    <div style="height:8px; border-bottom:1px dashed #d1cec9; margin-bottom:8px;"></div>
@endif
@endforeach

{{-- ══ CONSOLIDATED TOTALS ════════════════════════════════════════════════ --}}
@if (count($summaries) > 0)
<div class="totals-box">
    <div style="font-size:12px; font-weight:700; color:#db4a2b; margin-bottom:8px;">
        Consolidated Totals — All Documents
    </div>
    <table>
        <tbody>
            <tr>
                <td style="width:70%; border-bottom:1px solid #374151; color:#9ca3af;">
                    Total Gross Underwritten Premium (FTS)
                </td>
                <td class="right bold" style="border-bottom:1px solid #374151; color:#f1efea;">
                    ${{ number_format($totalFts, 2) }}
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #374151; color:#9ca3af;">
                    Total Deductions
                </td>
                <td class="right bold" style="border-bottom:1px solid #374151; color:#fca5a5;">
                    -${{ number_format($totalDeductions, 2) }}
                </td>
            </tr>
            <tr>
                <td style="border-bottom:none; color:#f1efea; font-weight:700; font-size:12px;">
                    Net Underwritten Premium
                </td>
                <td class="right bold" style="border-bottom:none; color:#86efac; font-size:12px;">
                    ${{ number_format($totalNet, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endif

</body>
</html>

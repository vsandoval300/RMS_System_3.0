<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Operative Document Overview — {{ $id ?? '' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f262a;
            background: #ffffff;
            padding: 20px 24px;
        }
        h1 { font-size: 15px; color: #db4a2b; font-weight: 700; }
        h3 { font-size: 11px; color: #db4a2b; font-weight: 600; margin: 10px 0 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { text-align: left; padding: 3px 5px; border-bottom: 1px solid #1f262a; font-weight: 600; color: #6b7280; }
        td { padding: 3px 5px; border-bottom: 1px solid #d1cec9; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .muted { color: #6b7280; }
        .section-header {
            background-color: #1f262a;
            color: #f1efea;
            padding: 5px 10px;
            font-weight: 700;
            font-size: 11px;
            margin-top: 12px;
            margin-bottom: 5px;
        }
        .section-box {
            border: 1px solid #d1cec9;
            padding: 8px 10px;
            margin-bottom: 8px;
        }
        .summary-box {
            background-color: #1f262a;
            color: #f1efea;
            padding: 10px 14px;
            margin-bottom: 12px;
        }
        .summary-box td { border-bottom: 1px solid #374151; color: #d1d5db; }
    </style>
</head>
<body>

@php
    $groupedNodesC  = collect($groupedCostNodes ?? []);
    $nodesFlat      = $groupedNodesC->flatMap(fn($g) => $g['nodes'] ?? [])->sortBy('index')->values();
    $grandDeductO   = $groupedNodesC->sum('subtotal_orig');

    // Fallback: when transactions have exch_rate=0, totalConvertedPremium is 0.
    // Use roe_fs from the document to derive the USD equivalents.
    $roeFs          = (float)($roe_fs ?? 0);
    $fts            = (float)($totalPremiumFts ?? 0);
    $convPremBase   = (float)($totalConvertedPremium ?? 0);
    if ($convPremBase <= 0 && $roeFs > 0) {
        $convPremBase = $fts / $roeFs;
    } elseif ($convPremBase <= 0) {
        $convPremBase = $fts; // same currency, no conversion
    }
    // Ratio to scale any orig amount to USD
    $convRatio      = $fts > 0 ? $convPremBase / $fts : 1.0;
    $grandDeductU   = $grandDeductO * $convRatio;
    $netOrig        = $fts - $grandDeductO;
    $netUsd         = $convPremBase - $grandDeductU;
    $txnsColl       = collect($transactions ?? []);
    $logsColl       = collect($logsByTxn ?? []);
    $insuredsC      = collect($insureds ?? []);
    $schemesC       = collect($costSchemes ?? []);
    $insuredsByScheme = $insuredsC->groupBy(fn($i) => $i['cscheme_id'] ?? $i['cost_scheme_id'] ?? '—');
    $schemeMetaById = $schemesC->mapWithKeys(function ($s) {
        $key = $s['cscheme_id'] ?? $s['id'] ?? null;
        return $key ? [$key => ['label' => $s['id'] ?? '—', 'share' => (float)($s['share'] ?? 0)]] : [];
    });
@endphp

{{-- ══ HEADER ══════════════════════════════════════════════════════════════ --}}
<table style="margin-bottom:12px;">
    <tr>
        <td style="border:none;">
            <h1>Operative Document Overview</h1>
            <div style="font-size:11px; font-weight:700; color:#1f262a; margin-top:2px;">{{ $id ?? '—' }}</div>
            <span class="muted" style="font-size:9px;">Generated: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </td>
        <td style="text-align:right; vertical-align:top; border:none;">
            <div style="font-size:9px; color:#6b7280;">RMS-System</div>
            @if (!empty($documentType))
            <div style="background:#1f262a; color:#f1efea; padding:2px 8px; border-radius:3px; font-size:9px; font-weight:600; display:inline-block; margin-top:4px;">{{ $documentType }}</div>
            @endif
        </td>
    </tr>
</table>

{{-- ══ GENERAL DETAILS ══════════════════════════════════════════════════════ --}}
<div class="section-box" style="margin-bottom:10px;">
    <table>
        <tr>
            <td style="width:22%; border:none; font-weight:600; color:#6b7280;">Document type:</td>
            <td style="width:28%; border:none;">{{ $documentType ?? '—' }}</td>
            <td style="width:22%; border:none; font-weight:600; color:#6b7280;">Premium type:</td>
            <td style="width:28%; border:none;">{{ $premiumType ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border:none; font-weight:600; color:#6b7280;">Currency:</td>
            <td style="border:none;">{{ $originalCurrency ?? '—' }}</td>
            <td style="border:none; font-weight:600; color:#6b7280;">Coverage days:</td>
            <td style="border:none;">{{ $coverageDays ?? '—' }}</td>
        </tr>
        <tr>
            <td style="border:none; font-weight:600; color:#6b7280;">Inception:</td>
            <td style="border:none;">{{ !empty($inceptionDate) ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '—' }}</td>
            <td style="border:none; font-weight:600; color:#6b7280;">Expiration:</td>
            <td style="border:none;">{{ !empty($expirationDate) ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '—' }}</td>
        </tr>
        <tr>
            <td style="border:none; font-weight:600; color:#6b7280;">RoE for Reference:</td>
            <td style="border:none;" colspan="3">{{ $roeFs > 0 ? number_format($roeFs, 8) : '—' }}</td>
        </tr>
    </table>
</div>

{{-- ══ FINANCIAL SUMMARY ════════════════════════════════════════════════════ --}}
<div class="summary-box">
    <div style="font-size:10px; font-weight:700; color:#db4a2b; margin-bottom:7px; text-transform:uppercase; letter-spacing:0.04em;">
        Financial Summary
    </div>
    <table>
        <thead>
            <tr>
                <th style="color:#9ca3af; border-bottom:1px solid #374151; width:60%;"></th>
                <th class="right" style="color:#9ca3af; border-bottom:1px solid #374151; white-space:nowrap;">
                    Orig. ({{ $originalCurrency ?? 'USD' }})
                </th>
                <th class="right" style="color:#9ca3af; border-bottom:1px solid #374151;">US Dollars</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color:#d1d5db; border-bottom:1px solid #374151;">Gross Underwritten Premium (FTS)</td>
                <td class="right bold" style="color:#f1efea; border-bottom:1px solid #374151;">${{ number_format($fts, 2) }}</td>
                <td class="right bold" style="color:#f1efea; border-bottom:1px solid #374151;">${{ number_format($convPremBase, 2) }}</td>
            </tr>
            <tr>
                <td style="color:#d1d5db; border-bottom:1px solid #374151;">Total Deductions</td>
                <td class="right bold" style="color:#fca5a5; border-bottom:1px solid #374151;">-${{ number_format($grandDeductO, 2) }}</td>
                <td class="right bold" style="color:#fca5a5; border-bottom:1px solid #374151;">-${{ number_format($grandDeductU, 2) }}</td>
            </tr>
            <tr>
                <td style="color:#f1efea; font-weight:700; font-size:11px; border-bottom:none;">Net Underwritten Premium</td>
                <td class="right bold" style="color:#86efac; font-size:11px; border-bottom:none;">${{ number_format($netOrig, 2) }}</td>
                <td class="right bold" style="color:#86efac; font-size:11px; border-bottom:none;">${{ number_format($netUsd, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ══ PLACEMENT SCHEMES ════════════════════════════════════════════════════ --}}
@if ($schemesC->isNotEmpty())
<div class="section-header">Placement Schemes</div>
<table style="margin-bottom:10px;">
    <thead>
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Description</th>
            <th class="right">Share</th>
            <th class="center">Agreement Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($schemesC as $idx => $scheme)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td style="font-size:9px; font-family:monospace;">{{ $scheme['id'] ?? '—' }}</td>
            <td>{{ $scheme['description'] ?? '—' }}</td>
            <td class="right">{{ number_format(($scheme['share'] ?? 0) * 100, 2) }}%</td>
            <td class="center">{{ $scheme['agreement_type'] ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ INSUREDS ══════════════════════════════════════════════════════════════ --}}
@if ($insuredsC->isNotEmpty())
<div class="section-header">Insureds</div>
@foreach ($insuredsByScheme as $schemeId => $rows)
@php
    $meta        = $schemeMetaById[$schemeId] ?? null;
    $schemeLabel = $meta['label'] ?? $schemeId;
    $schemeShare = (float) ($meta['share'] ?? 0);
@endphp
<div style="font-size:9px; font-weight:700; color:#db4a2b; margin:4px 0 2px; border-bottom:1px dashed #d1cec9; padding-bottom:2px;">
    Placement Scheme: {{ $schemeLabel }} ({{ number_format($schemeShare * 100, 2) }}%)
</div>
<table style="margin-bottom:6px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Insured</th>
            <th>Coverage</th>
            <th class="center">Country</th>
            <th class="right">Allocation</th>
            <th class="right">Annual Premium</th>
            <th class="right">Prem. Ftp</th>
            <th class="right">Prem. Fts</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows->values() as $idx => $insured)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $insured['company']['name'] ?? '—' }}</td>
            <td>{{ $insured['coverage']['name'] ?? '—' }}</td>
            <td class="center">{{ $insured['company']['country']['name'] ?? '—' }}</td>
            <td class="right">{{ number_format(($insured['allocation_percent'] ?? 0) * 100, 2) }}%</td>
            <td class="right">${{ number_format($insured['premium'] ?? 0, 2) }}</td>
            <td class="right">${{ number_format($insured['premium_ftp'] ?? 0, 2) }}</td>
            <td class="right">${{ number_format($insured['premium_fts'] ?? 0, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background:#f1efea;">
            <td colspan="5" class="right muted" style="font-size:9px;">Subtotals:</td>
            <td class="right bold">${{ number_format($rows->sum('premium'), 2) }}</td>
            <td class="right bold">${{ number_format($rows->sum(fn($i) => $i['premium_ftp'] ?? 0), 2) }}</td>
            <td class="right bold">${{ number_format($rows->sum(fn($i) => $i['premium_fts'] ?? 0), 2) }}</td>
        </tr>
    </tbody>
</table>
@endforeach
@endif

{{-- ══ COSTS BREAKDOWN ══════════════════════════════════════════════════════ --}}
@if ($groupedNodesC->isNotEmpty())
<div class="section-header">Costs Breakdown</div>
<table style="margin-bottom:10px;">
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th style="width:28%;">Partner</th>
            <th style="width:28%;">Concept</th>
            <th class="right" style="width:10%;">Rate</th>
            <th class="right" style="width:15%;">Orig. Curr.</th>
            <th class="right" style="width:14%;">US Dollars</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3" class="right bold" style="border-bottom:2px solid #1f262a;">Gross Underwritten Premium</td>
            <td style="border-bottom:2px solid #1f262a;"></td>
            <td class="right bold" style="border-bottom:2px solid #1f262a;">${{ number_format($fts, 2) }}</td>
            <td class="right bold" style="border-bottom:2px solid #1f262a;">${{ number_format($convPremBase, 2) }}</td>
        </tr>
        @foreach ($groupedNodesC as $group)
            @foreach ($group['nodes'] as $node)
            @php
                $nodeOrigAmt = (float)($node['deduction_amount'] ?? 0);
                $nodeUsdAmt  = (float)($node['deduction_usd']    ?? 0);
                if ($nodeUsdAmt == 0 && $nodeOrigAmt > 0) {
                    $nodeUsdAmt = $nodeOrigAmt * $convRatio;
                }
            @endphp
            <tr>
                <td>{{ $node['index'] }}</td>
                <td>{{ $node['partner'] ?? '—' }}</td>
                <td>{{ $node['deduction'] ?? '—' }}</td>
                <td class="right">{{ number_format(($node['value'] ?? 0) * 100, 2) }}%</td>
                <td class="right" style="color:#dc2626;">-${{ number_format($nodeOrigAmt, 2) }}</td>
                <td class="right" style="color:#dc2626;">-${{ number_format($nodeUsdAmt, 2) }}</td>
            </tr>
            @endforeach
            @php
                $subtotalOrig = (float)($group['subtotal_orig'] ?? 0);
                $subtotalUsd  = (float)($group['subtotal_usd']  ?? 0);
                if ($subtotalUsd == 0 && $subtotalOrig > 0) {
                    $subtotalUsd = $subtotalOrig * $convRatio;
                }
            @endphp
            <tr style="background:#f1efea;">
                <td colspan="4" class="right muted" style="font-size:9px;">Share {{ number_format(($group['share'] ?? 0) * 100, 2) }}% subtotal:</td>
                <td class="right bold">-${{ number_format($subtotalOrig, 2) }}</td>
                <td class="right bold">-${{ number_format($subtotalUsd, 2) }}</td>
            </tr>
        @endforeach
        <tr style="border-top:2px solid #1f262a;">
            <td colspan="4" class="right bold">Total Deductions:</td>
            <td class="right bold" style="color:#dc2626;">-${{ number_format($grandDeductO, 2) }}</td>
            <td class="right bold" style="color:#dc2626;">-${{ number_format($grandDeductU, 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="right bold" style="font-size:11px;">Net Underwritten Premium:</td>
            <td class="right bold" style="font-size:11px;">${{ number_format($netOrig, 2) }}</td>
            <td class="right bold" style="font-size:11px;">${{ number_format($netUsd, 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

{{-- ══ INSTALLMENTS ══════════════════════════════════════════════════════════ --}}
@if ($txnsColl->isNotEmpty())
<div class="section-header">Installments</div>
<table style="margin-bottom:10px;">
    <thead>
        <tr>
            <th>#</th>
            <th class="right">Proportion</th>
            <th class="right">Exch. Rate</th>
            <th class="center">Due Date</th>
            <th class="right">Orig. Curr.</th>
            <th class="right">US Dollars</th>
        </tr>
    </thead>
    <tbody>
        @php $gOrig = 0; $gUsd = 0; @endphp
        @foreach ($txnsColl as $txn)
        @php
            $prop   = (float)($txn['proportion'] ?? 0);
            $rate   = (float)($txn['exch_rate']  ?? 0);
            $aOrig  = $netOrig * $prop;
            $aUsd   = $rate > 0 ? $aOrig / $rate : 0;
            $gOrig += $aOrig;
            $gUsd  += $aUsd;
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

{{-- ══ TRANSACTIONS LIFECYCLE ════════════════════════════════════════════════ --}}
@if ($nodesFlat->isNotEmpty())
<div class="section-header">Transactions Lifecycle</div>
@foreach ($txnsColl as $tIdx => $txn)
@php
    $txnId   = $txn['id'] ?? null;
    $pRaw    = (float)($txn['proportion'] ?? 0);
    $pPct    = ($pRaw > 1 ? $pRaw : $pRaw * 100);
    $txnLogs = $txnId ? collect($logsColl[$txnId] ?? []) : collect();
@endphp
<div style="margin-top:6px; margin-bottom:3px; font-weight:700; font-size:10px; background:#e8e6e1; padding:3px 6px;">
    Installment {{ $txn['index'] ?? ($tIdx + 1) }}
    <span style="font-weight:400; color:#6b7280; font-size:9px;">
        — {{ number_format($pPct, 2) }}% · Rate: {{ number_format((float)($txn['exch_rate'] ?? 0), 4) }}
        · Due: {{ isset($txn['due_date']) ? \Carbon\Carbon::parse($txn['due_date'])->format('d/m/Y') : '—' }}
    </span>
</div>
<table style="margin-bottom:5px;">
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
            $nodeIndex = (int)($node['index'] ?? ($nIdx + 1));
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

</body>
</html>

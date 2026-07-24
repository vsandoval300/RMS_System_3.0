<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Business Summary — {{ $business?->business_code ?? '' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            color: #1f262a;
            background: #ffffff;
            padding: 28px 40px;
        }
        h1 { font-size: 14px; color: #db4a2b; font-weight: 700; }
        h2 { font-size: 10px; color: #1f262a; font-weight: 700; margin-bottom: 4px; }
        h3 { font-size: 9px; color: #db4a2b; font-weight: 600; margin: 8px 0 3px; }
        table { width: 100%; border-collapse: collapse; font-size: 8px; }
        th { text-align: left; padding: 2px 4px; border-bottom: 1px solid #1f262a; font-weight: 600; color: #6b7280; }
        td { padding: 2px 4px; border-bottom: 1px solid #d1cec9; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .accent { color: #db4a2b; }
        .muted { color: #6b7280; }
        .doc-header {
            background-color: #1f262a;
            color: #f1efea;
            padding: 5px 8px;
            font-weight: 700;
            font-size: 9px;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .section-box {
            border: 1px solid #d1cec9;
            padding: 6px 8px;
            margin-bottom: 6px;
        }
        .totals-box {
            background-color: #1f262a;
            color: #f1efea;
            padding: 8px 12px;
            margin-top: 10px;
        }
        .totals-box td { border-bottom: 1px solid #374151; color: #d1d5db; }
        .totals-box .net td { border-top: 2px solid #4b5563; color: #f1efea; font-weight: 700; }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 7px;
            font-weight: 700;
        }
        .page-break { page-break-after: always; }
        @page {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

{{-- ══ HEADER ══════════════════════════════════════════════════════════════ --}}
<table style="margin-bottom:16px; border-bottom:2px solid #db4a2b; padding-bottom:10px;">
    <tr>
        <td style="border:none; vertical-align:bottom;">
            <h1 style="font-size:16px; margin-bottom:3px;">Business Summary</h1>
            <span class="muted" style="font-size:7px;">Generated: {{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i') }}</span>
        </td>
        <td style="text-align:right; vertical-align:top; border:none; padding-top:3px;">
            <span style="font-size:8px; font-weight:700; color:#1f262a; letter-spacing:0.5px;">RMS-Platform</span>
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
    $docId       = $summary['id']               ?? '—';
    $docType     = $summary['documentType']     ?? '—';
    $inception   = $summary['inceptionDate']    ?? null;
    $expiry      = $summary['expirationDate']   ?? null;
    $currency    = $summary['originalCurrency'] ?? '—';
    $premiumType = $summary['premiumType']      ?? '—';
    $coverageDays= $summary['coverageDays']     ?? '—';
    $createdAt   = $summary['createdAt']        ?? null;
    $fts         = (float) ($summary['totalPremiumFts'] ?? 0);

    $groupedNodes    = collect($summary['groupedCostNodes'] ?? []);
    $nodesFlat       = $groupedNodes->flatMap(fn($g) => $g['nodes'] ?? [])->sortBy('index')->values();
    $grandDeductOrig = $groupedNodes->sum('subtotal_orig');
    $transactions    = collect($summary['transactions'] ?? []);
    $logsByTxn       = collect($summary['logsByTxn']   ?? []);

    $docSchemes  = collect($summary['costSchemes'] ?? []);
    $docInsureds = collect($summary['insureds']    ?? []);
    $docSchemeMeta = $docSchemes->mapWithKeys(function ($s) {
        $key = $s['cscheme_id'] ?? $s['id'] ?? null;
        return $key ? [$key => ['label' => $s['id'] ?? '—', 'share' => (float)($s['share'] ?? 0)]] : [];
    });
    $docInsuredsGrouped = $docInsureds->groupBy(fn ($i) => $i['cscheme_id'] ?? $i['cost_scheme_id'] ?? '—');

    // USD fallback: use roe_fs when exch_rate=0 causes totalConvertedPremium=0
    $docRoeFs = (float) ($summary['roe_fs'] ?? 0);
    $convPrem = (float) ($summary['totalConvertedPremium'] ?? 0);
    if ($convPrem <= 0 && $docRoeFs > 0) {
        $convPrem = $fts / $docRoeFs;
    } elseif ($convPrem <= 0) {
        $convPrem = $fts;
    }
    $convRatioDoc    = $fts > 0 ? $convPrem / $fts : 1.0;
    $grandDeductUsd  = $grandDeductOrig * $convRatioDoc;
    $netOrig = $fts - $grandDeductOrig;
    $netUsd  = $convPrem - $grandDeductUsd;
@endphp

<div class="doc-header">
    {{ sprintf('%02d', $sIdx + 1) }}. {{ $docId }} — {{ $docType }}
    @if ($inception && $expiry)
        <span style="font-weight:400; color:#9ca3af; font-size:7px; margin-left:8px;">
            {{ \Carbon\Carbon::parse($inception)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($expiry)->format('d/m/Y') }}
        </span>
    @endif
    <span style="float:right; color:#db4a2b; font-size:7px; font-weight:400;">Net: ${{ number_format($netOrig, 2) }} {{ $currency }}</span>
</div>

{{-- General Details --}}
<div style="background:#374151; color:#f1efea; padding:4px 8px; font-weight:600; font-size:7px; margin-bottom:4px; display:table; width:100%;">
    <span>General Details</span>
    <span style="float:right; color:#9ca3af; font-weight:400;">{{ $premiumType }} · {{ $currency }}</span>
</div>
<table style="margin-bottom:8px; table-layout:fixed;">
    <colgroup>
        <col style="width:22%;">
        <col style="width:28%;">
        <col style="width:22%;">
        <col style="width:28%;">
    </colgroup>
    <tbody>
        <tr>
            <td class="bold muted">Document type:</td>
            <td>{{ $docType }}</td>
            <td class="bold muted">Creation date:</td>
            <td>{{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '—' }}</td>
        </tr>
        <tr>
            <td class="bold muted">Premium type:</td>
            <td>{{ $premiumType }}</td>
            <td class="bold muted">Period:</td>
            <td>
                {{ $inception ? \Carbon\Carbon::parse($inception)->format('d/m/Y') : '—' }}
                to
                {{ $expiry ? \Carbon\Carbon::parse($expiry)->format('d/m/Y') : '—' }}
            </td>
        </tr>
        <tr>
            <td class="bold muted">Original currency:</td>
            <td>{{ $currency }}</td>
            <td class="bold muted">Coverage days:</td>
            <td>{{ $coverageDays }}</td>
        </tr>
        <tr>
            <td class="bold muted">RoE for Reference:</td>
            <td colspan="3">{{ $docRoeFs > 0 ? number_format($docRoeFs, 8) : '—' }}</td>
        </tr>
    </tbody>
</table>

{{-- Placement Schemes --}}
@if ($docSchemes->isNotEmpty())
<div style="background:#374151; color:#f1efea; padding:4px 8px; font-weight:600; font-size:7px; margin-bottom:4px; display:table; width:100%;">
    <span>Placement Schemes</span>
    <span style="float:right; color:#9ca3af; font-weight:400;">{{ $docSchemes->count() }} scheme(s)</span>
</div>
<table style="margin-bottom:8px;">
    <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th style="width:20%;">ID</th>
            <th>Description</th>
            <th class="right" style="width:12%;">Share (%)</th>
            <th class="center" style="width:18%;">Agreement Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($docSchemes as $idx => $scheme)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td style="font-size:7px;">{{ $scheme['id'] ?? '—' }}</td>
            <td>{{ $scheme['description'] ?? '—' }}</td>
            <td class="right bold">{{ number_format(($scheme['share'] ?? 0) * 100, 2) }}%</td>
            <td class="center">{{ $scheme['agreement_type'] ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Insureds --}}
@if ($docInsureds->isNotEmpty())
<div style="background:#374151; color:#f1efea; padding:4px 8px; font-weight:600; font-size:7px; margin-bottom:4px; display:table; width:100%;">
    <span>Insureds</span>
    <span style="float:right; color:#9ca3af; font-weight:400;">{{ $docInsureds->count() }} member(s)</span>
</div>
@foreach ($docInsuredsGrouped as $schemeId => $rows)
@php
    $sMeta  = $docSchemeMeta[$schemeId] ?? null;
    $sLabel = $sMeta['label'] ?? $schemeId;
    $sShare = (float)($sMeta['share'] ?? 0);
@endphp
<div style="font-weight:600; font-size:7px; color:#db4a2b; margin:4px 0 2px; border-bottom:1px solid #d1cec9; padding-bottom:2px;">
    Placement Scheme: {{ $sLabel }} ({{ number_format($sShare * 100, 2) }}%)
</div>
<table style="margin-bottom:6px; table-layout:fixed; width:100%;">
    <thead>
        <tr>
            <th style="width:1%; white-space:nowrap; padding-right:4px;">#</th>
            <th style="width:31%;">Insured</th>
            <th style="width:18%;">Coverage</th>
            <th class="center" style="width:12%;">Country</th>
            <th class="right" style="width:10%;">Allocation</th>
            <th class="right" style="width:14%;">Annual Premium</th>
            <th class="right" style="width:14%;">Prem. Fts</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows->values() as $idx => $insured)
        <tr>
            <td style="white-space:nowrap; padding-right:4px;">{{ $idx + 1 }}</td>
            <td style="word-wrap:break-word; overflow-wrap:break-word;">{{ $insured['company']['name'] ?? '—' }}</td>
            <td>{{ $insured['coverage']['name'] ?? '—' }}</td>
            <td class="center">{{ $insured['company']['country']['name'] ?? '—' }}</td>
            <td class="right">{{ number_format(($insured['allocation_percent'] ?? 0) * 100, 2) }}%</td>
            <td class="right">${{ number_format($insured['premium'] ?? 0, 2) }}</td>
            <td class="right">${{ number_format($insured['premium_fts'] ?? 0, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background:#f1efea;">
            <td colspan="5" class="right bold muted" style="font-size:7px;">Totals:</td>
            <td class="right bold">${{ number_format($rows->sum('premium'), 2) }}</td>
            <td class="right bold">${{ number_format($rows->sum(fn($i) => $i['premium_fts'] ?? 0), 2) }}</td>
        </tr>
    </tbody>
</table>
@endforeach
@endif

{{-- Costs Breakdown --}}
@if ($groupedNodes->isNotEmpty())
<div style="background:#374151; color:#f1efea; padding:4px 8px; font-weight:600; font-size:7px; margin-bottom:4px; display:table; width:100%;">
    <span>Costs Breakdown</span>
    <span style="float:right; color:#9ca3af; font-weight:400;">Total deductions: -${{ number_format($grandDeductOrig, 2) }}</span>
</div>
<table style="margin-bottom:8px; table-layout:fixed; width:100%;">
    <thead>
        <tr>
            <th style="width:40%;">Partner</th>
            <th style="width:18%;">Concept</th>
            <th class="right" style="width:14%;">Rate</th>
            <th class="right" style="width:14%;">Orig. Curr.</th>
            <th class="right" style="width:14%;">USD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedNodes as $group)
            @foreach ($group['nodes'] as $node)
            @php
                $nOrig = (float)($node['deduction_amount'] ?? 0);
                $nUsd  = (float)($node['deduction_usd']    ?? 0);
                if ($nUsd == 0 && $nOrig > 0) { $nUsd = $nOrig * $convRatioDoc; }
            @endphp
            <tr>
                <td><span class="muted" style="font-size:7px;">{{ $node['index'] }}.</span> {{ $node['partner'] ?? '—' }}</td>
                <td>{{ $node['deduction'] ?? '—' }}</td>
                <td class="right">{{ number_format($node['value'] * 100, 2) }}%</td>
                <td class="right" style="color:#dc2626;">-${{ number_format($nOrig, 2) }}</td>
                <td class="right" style="color:#dc2626;">-${{ number_format($nUsd, 2) }}</td>
            </tr>
            @endforeach
            @php
                $gOrig = (float)($group['subtotal_orig'] ?? 0);
                $gUsd  = $gOrig * $convRatioDoc;
            @endphp
            <tr style="background:#f1efea;">
                <td colspan="3" class="right muted" style="font-size:7px;">Share {{ number_format($group['share'] * 100, 2) }}% subtotal:</td>
                <td class="right bold">-${{ number_format($gOrig, 2) }}</td>
                <td class="right bold">-${{ number_format($gUsd, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Financial Summary --}}
<table style="margin-bottom:8px; table-layout:fixed; width:100%;">
    <thead>
        <tr>
            <th style="width:72%;"></th>
            <th class="right" style="width:14%;">Orig. Curr. ({{ $currency }})</th>
            <th class="right" style="width:14%;">US Dollars</th>
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
            <td class="bold" style="font-size:9px;">Net Underwritten Premium</td>
            <td class="right bold" style="font-size:9px;">${{ number_format($netOrig, 2) }}</td>
            <td class="right bold" style="font-size:9px;">${{ number_format($netUsd, 2) }}</td>
        </tr>
    </tbody>
</table>

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
<div style="margin-top:6px; margin-bottom:4px; font-weight:700; font-size:8px; background:#e8e6e1; padding:3px 6px;">
    Installment {{ $txn['index'] ?? ($tIdx + 1) }}
    <span style="font-weight:400; color:#6b7280; font-size:7px;">
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
    <div style="font-size:10px; font-weight:700; color:#db4a2b; margin-bottom:8px;">
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
                <td style="border-bottom:none; color:#f1efea; font-weight:700; font-size:10px;">
                    Net Underwritten Premium
                </td>
                <td class="right bold" style="border-bottom:none; color:#86efac; font-size:10px;">
                    ${{ number_format($totalNet, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $font  = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $size  = 7;
            $text  = "Page $pageNumber of $pageCount";
            $w     = $canvas->get_width();
            $h     = $canvas->get_height();
            $tw    = $fontMetrics->get_text_width($text, $font, $size);
            $x     = ($w - $tw) / 2;
            $y     = $h - 22;
            $gray  = [0.61, 0.64, 0.67];
            $light = [0.898, 0.886, 0.875];
            $canvas->line(40, $y - 6, $w - 40, $y - 6, $light, 0.5);
            $canvas->text($x, $y, $text, $font, $size, $gray);
        });
    }
</script>

</body>
</html>

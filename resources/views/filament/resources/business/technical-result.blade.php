@php
    use Carbon\Carbon;

    $business    = $business    ?? null;
    $summaries   = $summaries   ?? [];
    $generatedAt = $generatedAt ?? now();
    $totalFts        = (float) ($totalFts        ?? 0);
    $totalFtp        = (float) ($totalFtp        ?? 0);
    $totalDeductions = (float) ($totalDeductions ?? 0);
    $totalNet        = (float) ($totalNet        ?? 0);

    $lifecycleStatus   = $business?->business_lifecycle_status;
    $lifecycleValue    = $lifecycleStatus?->value ?? '—';

    $approvalStatus    = $business?->approval_status;
    $approvalValue     = $approvalStatus?->value ?? '—';

    $lifecycleColors = match ($lifecycleValue) {
        'In Force'  => ['#14532d', '#86efac'],
        'To Expire' => ['#713f12', '#fde047'],
        'Expired'   => ['#7f1d1d', '#fca5a5'],
        'Cancelled' => ['#27272a', '#9ca3af'],
        default     => ['#374151', '#d1d5db'],
    };

    $approvalColors = match ($approvalValue) {
        'Approved'        => ['#052e16', '#86efac'],
        'Pending Review'  => ['#1c1a0e', '#fbbf24'],
        'Needs Revision'  => ['#1c0a0a', '#fca5a5'],
        default           => ['#27272a', '#9ca3af'],
    };
@endphp

<style>
    .tr-report details > summary {
        list-style: none;
        justify-content: flex-start !important;
        gap: 8px !important;
    }
    .tr-report details > summary::-webkit-details-marker { display: none; }
    .tr-report details > summary::before {
        content: "▶";
        flex-shrink: 0;
        font-size: 9px;
        opacity: 0.75;
    }
    .tr-report details[open] > summary::before {
        content: "▼";
    }
    .tr-report details > summary > span:last-child {
        margin-left: auto;
    }
</style>
<div class="tr-report" style="
    background-color: #f1efea;
    color: #1f262a;
    padding: 24px;
    font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    font-size: 13px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    overflow-x: hidden;
">

{{-- ══ TOP BAR ══════════════════════════════════════════════════════════════ --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:2px solid #db4a2b; padding-bottom:10px;">
    <div>
        <span style="font-size:18px; font-weight:700; color:#db4a2b;">Business Summary</span>
        <span style="margin-left:12px; color:#6b7280; font-size:12px;">
            Generated: {{ Carbon::parse($generatedAt)->format('d/m/Y H:i') }}
        </span>
    </div>
    @if ($business)
        <a href="{{ route('business.technical-result.pdf', $business->business_code) }}"
           target="_blank"
           style="
               display:inline-flex; align-items:center; gap:6px;
               background:#1f262a; color:#f1efea;
               padding:6px 14px; border-radius:6px;
               font-size:12px; font-weight:600; text-decoration:none;
           ">
            ↓ Download PDF
        </a>
    @endif
</div>

{{-- ══ BUSINESS HEADER ═══════════════════════════════════════════════════════ --}}
@if ($business)
<div style="background:#fff; border:1px solid #d1cec9; border-radius:6px; padding:14px 18px; margin-bottom:20px;">
    <div style="font-size:16px; font-weight:700; color:#1f262a; margin-bottom:10px;">
        {{ $business->business_code }}
        @if ($business->description)
            <span style="font-weight:400; color:#6b7280; font-size:13px; margin-left:8px;">— {{ $business->description }}</span>
        @endif
    </div>
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; font-size:12px;">
        <div>
            <span style="font-weight:600; color:#6b7280;">Reinsurer:</span><br>
            {{ $business->reinsurer?->name ?? '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Currency:</span><br>
            {{ $business->currency?->acronym ?? '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Reinsurance Type:</span><br>
            {{ $business->reinsurance_type ?? '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Premium Type:</span><br>
            {{ $business->premium_type ?? '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Lifecycle:</span><br>
            <span style="
                display:inline-block; padding:2px 10px; border-radius:9999px; font-size:11px; font-weight:600;
                background-color:{{ $lifecycleColors[0] }}; color:{{ $lifecycleColors[1] }};
            ">{{ $lifecycleValue }}</span>
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Approval:</span><br>
            <span style="
                display:inline-block; padding:2px 10px; border-radius:9999px; font-size:11px; font-weight:600;
                background-color:{{ $approvalColors[0] }}; color:{{ $approvalColors[1] }};
            ">{{ $approvalValue }}</span>
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Producer:</span><br>
            {{ $business->producer?->name ?? '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#6b7280;">Documents:</span><br>
            {{ count($summaries) }} operative doc(s)
        </div>
    </div>
</div>
@endif

{{-- ══ PER-DOCUMENT SECTIONS ═══════════════════════════════════════════════ --}}
@forelse ($summaries as $sIdx => $summary)
@php
    $docId          = $summary['id']             ?? '—';
    $docType        = $summary['documentType']   ?? '—';
    $inceptionDate  = $summary['inceptionDate']  ?? null;
    $expirationDate = $summary['expirationDate'] ?? null;
    $currency       = $summary['originalCurrency'] ?? '—';
    $fts            = (float) ($summary['totalPremiumFts']    ?? 0);
    $deductions     = (float) ($summary['totalDeductionOrig'] ?? 0);
    $net            = $fts - $deductions;
    $transactions   = collect($summary['transactions']   ?? []);
    $logsByTxn      = collect($summary['logsByTxn']      ?? []);
    $groupedNodes   = collect($summary['groupedCostNodes'] ?? []);
    $nodesFlat      = $groupedNodes->flatMap(fn($g) => $g['nodes'] ?? [])->sortBy('index')->values();

    // USD fallback: when exch_rate=0 on transactions, totalConvertedPremium is 0.
    // Use roe_fs from the document as conversion basis.
    $docRoeFs       = (float) ($summary['roe_fs'] ?? 0);
    $convPremDoc    = (float) ($summary['totalConvertedPremium'] ?? 0);
    if ($convPremDoc <= 0 && $docRoeFs > 0) {
        $convPremDoc = $fts / $docRoeFs;
    } elseif ($convPremDoc <= 0) {
        $convPremDoc = $fts;
    }
    $convRatioDoc   = $fts > 0 ? $convPremDoc / $fts : 1.0;

    $docSchemes     = collect($summary['costSchemes'] ?? []);
    $docInsureds    = collect($summary['insureds']    ?? []);
    $docSchemeMeta  = $docSchemes->mapWithKeys(function ($s) {
        $key = $s['cscheme_id'] ?? $s['id'] ?? null;
        return $key ? [$key => ['label' => $s['id'] ?? '—', 'share' => (float)($s['share'] ?? 0)]] : [];
    });
    $docInsuredsGrouped = $docInsureds->groupBy(fn ($i) => $i['cscheme_id'] ?? $i['cost_scheme_id'] ?? '—');
@endphp

<details open style="margin-bottom:16px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="
        background:#1f262a; color:#f1efea; padding:10px 16px;
        cursor:pointer; font-weight:600; font-size:13px; list-style:none;
        display:flex; justify-content:space-between; align-items:center;
    ">
        <span>
            {{ sprintf('%02d', $sIdx + 1) }}. {{ $docId }} — {{ $docType }}
            @if ($inceptionDate && $expirationDate)
                <span style="font-weight:400; color:#9ca3af; font-size:12px; margin-left:10px;">
                    {{ Carbon::parse($inceptionDate)->format('d/m/Y') }}
                    →
                    {{ Carbon::parse($expirationDate)->format('d/m/Y') }}
                </span>
            @endif
        </span>
        <span style="color:#db4a2b; font-size:12px; font-weight:400;">Net: ${{ number_format($net, 2) }} {{ $currency }}</span>
    </summary>

    <div style="padding:16px; background:#faf9f7;">

        {{-- General Details --}}
        <details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
            <summary style="background:#374151; color:#f1efea; padding:6px 12px; cursor:pointer; font-weight:600; font-size:11px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
                General Details
                <span style="color:#9ca3af; font-weight:400;">{{ $summary['premiumType'] ?? '—' }} · {{ $currency }}</span>
            </summary>
            <div style="padding:10px 12px; background:#ffffff;">
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <tbody>
                        <tr>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280; width:22%;">Document type:</td>
                            <td style="padding:4px 6px; width:28%; border-bottom:1px solid #f0eeeb;">{{ $docType }}</td>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280; width:22%;">Creation date:</td>
                            <td style="padding:4px 6px; border-bottom:1px solid #f0eeeb;">{{ isset($summary['createdAt']) ? \Carbon\Carbon::parse($summary['createdAt'])->format('d/m/Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280;">Premium type:</td>
                            <td style="padding:4px 6px; border-bottom:1px solid #f0eeeb;">{{ $summary['premiumType'] ?? '—' }}</td>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280;">Period:</td>
                            <td style="padding:4px 6px; border-bottom:1px solid #f0eeeb;">
                                {{ $inceptionDate ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '—' }}
                                to
                                {{ $expirationDate ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280;">Original currency:</td>
                            <td style="padding:4px 6px; border-bottom:1px solid #f0eeeb;">{{ $currency }}</td>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280;">Coverage days:</td>
                            <td style="padding:4px 6px; border-bottom:1px solid #f0eeeb;">{{ $summary['coverageDays'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 6px; font-weight:600; color:#6b7280;">RoE for Reference:</td>
                            <td style="padding:4px 6px;" colspan="3">{{ $docRoeFs > 0 ? number_format($docRoeFs, 8) : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </details>

        {{-- Placement Schemes --}}
        @if ($docSchemes->isNotEmpty())
        <details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
            <summary style="background:#374151; color:#f1efea; padding:6px 12px; cursor:pointer; font-weight:600; font-size:11px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
                Placement Schemes
                <span style="color:#9ca3af; font-weight:400;">{{ $docSchemes->count() }} scheme(s)</span>
            </summary>
            <div style="padding:10px 12px; background:#ffffff;">
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="border-bottom:1px solid #d1cec9;">
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">#</th>
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">ID</th>
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">Description</th>
                            <th style="text-align:center; padding:3px 6px; color:#6b7280;">Share (%)</th>
                            <th style="text-align:center; padding:3px 6px; color:#6b7280;">Agreement Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($docSchemes as $idx => $scheme)
                        <tr style="border-bottom:1px solid #f0eeeb;">
                            <td style="padding:3px 6px;">{{ $idx + 1 }}</td>
                            <td style="padding:3px 6px; font-family:monospace; font-size:11px;">{{ $scheme['id'] ?? '—' }}</td>
                            <td style="padding:3px 6px;">{{ $scheme['description'] ?? '—' }}</td>
                            <td style="padding:3px 6px; text-align:center; font-weight:600;">{{ number_format(($scheme['share'] ?? 0) * 100, 2) }}%</td>
                            <td style="padding:3px 6px; text-align:center;">{{ $scheme['agreement_type'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
        @endif

        {{-- Insureds --}}
        @if ($docInsureds->isNotEmpty())
        <details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
            <summary style="background:#374151; color:#f1efea; padding:6px 12px; cursor:pointer; font-weight:600; font-size:11px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
                Insureds
                <span style="color:#9ca3af; font-weight:400;">{{ $docInsureds->count() }} member(s)</span>
            </summary>
            <div style="padding:10px 12px; background:#ffffff; overflow-x:auto;">
                @foreach ($docInsuredsGrouped as $schemeId => $rows)
                @php
                    $sMeta  = $docSchemeMeta[$schemeId] ?? null;
                    $sLabel = $sMeta['label'] ?? $schemeId;
                    $sShare = (float)($sMeta['share'] ?? 0);
                @endphp
                <div style="font-weight:600; font-size:11px; color:#db4a2b; margin:6px 0 3px; border-bottom:1px solid #d1cec9; padding-bottom:2px;">
                    Placement Scheme: {{ $sLabel }} ({{ number_format($sShare * 100, 2) }}%)
                </div>
                <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-size:12px; margin-bottom:8px;">
                    <colgroup>
                        <col style="width:3%;">
                        <col style="width:29%;">
                        <col style="width:18%;">
                        <col style="width:12%;">
                        <col style="width:10%;">
                        <col style="width:14%;">
                        <col style="width:14%;">
                    </colgroup>
                    <thead>
                        <tr style="border-bottom:1px solid #d1cec9;">
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">#</th>
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">Insured</th>
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">Coverage</th>
                            <th style="text-align:center; padding:3px 6px; color:#6b7280;">Country</th>
                            <th style="text-align:right; padding:3px 6px; color:#6b7280;">Allocation</th>
                            <th style="text-align:right; padding:3px 6px; color:#6b7280;">Annual Premium</th>
                            <th style="text-align:right; padding:3px 6px; color:#6b7280;">Prem. Fts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows->values() as $idx => $insured)
                        <tr style="border-bottom:1px solid #f0eeeb;">
                            <td style="padding:3px 6px;">{{ $idx + 1 }}</td>
                            <td style="padding:3px 6px; word-wrap:break-word; overflow-wrap:break-word;" title="{{ $insured['company']['name'] ?? '—' }}">{{ $insured['company']['name'] ?? '—' }}</td>
                            <td style="padding:3px 6px;">{{ $insured['coverage']['name'] ?? '—' }}</td>
                            <td style="padding:3px 6px; text-align:center;">{{ $insured['company']['country']['name'] ?? '—' }}</td>
                            <td style="padding:3px 6px; text-align:right;">{{ number_format(($insured['allocation_percent'] ?? 0) * 100, 2) }}%</td>
                            <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium'] ?? 0, 2) }}</td>
                            <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium_fts'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr style="background:#f5f4f2; border-top:1px solid #d1cec9;">
                            <td colspan="5" style="padding:3px 6px; text-align:right; font-weight:600; color:#6b7280; font-size:11px;">Totals:</td>
                            <td style="padding:3px 6px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($rows->sum('premium'), 2) }}</td>
                            <td style="padding:3px 6px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($rows->sum(fn($i) => $i['premium_fts'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                @endforeach
            </div>
        </details>
        @endif

        {{-- Costs Breakdown --}}
        @if ($groupedNodes->isNotEmpty())
        <details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
            <summary style="background:#374151; color:#f1efea; padding:6px 12px; cursor:pointer; font-weight:600; font-size:11px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
                Costs Breakdown
                <span style="color:#9ca3af; font-weight:400;">Total deductions: -${{ number_format($groupedNodes->sum('subtotal_orig'), 2) }}</span>
            </summary>
            <div style="padding:10px 12px; background:#ffffff;">
                <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-size:12px;">
                    <colgroup>
                        <col style="width:3%;">
                        <col style="width:35%;">
                        <col style="width:22%;">
                        <col style="width:12%;">
                        <col style="width:14%;">
                        <col style="width:14%;">
                    </colgroup>
                    <thead>
                        <tr style="border-bottom:1px solid #d1cec9;">
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">#</th>
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">Partner</th>
                            <th style="text-align:left; padding:3px 6px; color:#6b7280;">Concept</th>
                            <th style="text-align:right; padding:3px 6px; color:#6b7280;">Rate</th>
                            <th style="text-align:right; padding:3px 6px; color:#6b7280;">Orig. Curr.</th>
                            <th style="text-align:right; padding:3px 6px; color:#6b7280;">USD</th>
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
                            <tr style="border-bottom:1px solid #f0eeeb;">
                                <td style="padding:3px 6px;">{{ $node['index'] }}</td>
                                <td style="padding:3px 6px;">{{ $node['partner'] ?? '—' }}</td>
                                <td style="padding:3px 6px;">{{ $node['deduction'] ?? '—' }}</td>
                                <td style="padding:3px 6px; text-align:right;">{{ number_format($node['value'] * 100, 2) }}%</td>
                                <td style="padding:3px 6px; text-align:right; color:#dc2626;">-${{ number_format($nOrig, 2) }}</td>
                                <td style="padding:3px 6px; text-align:right; color:#dc2626;">-${{ number_format($nUsd, 2) }}</td>
                            </tr>
                            @endforeach
                            @php
                                $gOrig = (float)($group['subtotal_orig'] ?? 0);
                                $gUsd  = (float)($group['subtotal_usd']  ?? 0);
                                if ($gUsd == 0 && $gOrig > 0) { $gUsd = $gOrig * $convRatioDoc; }
                            @endphp
                            <tr style="background:#f5f4f2; border-bottom:1px solid #d1cec9;">
                                <td colspan="4" style="padding:3px 6px; text-align:right; font-weight:600; font-size:11px; color:#6b7280;">
                                    Share {{ number_format($group['share'] * 100, 2) }}% subtotal:
                                </td>
                                <td style="padding:3px 6px; text-align:right; font-weight:600;">-${{ number_format($gOrig, 2) }}</td>
                                <td style="padding:3px 6px; text-align:right; font-weight:600;">-${{ number_format($gUsd, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
        @endif

        {{-- Financial Summary --}}
        <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-size:12px; margin-bottom:16px;">
            <colgroup>
                <col style="width:72%;">
                <col style="width:14%;">
                <col style="width:14%;">
            </colgroup>
            <thead>
                <tr style="border-bottom:1px solid #100f0d;">
                    <th style="text-align:left; padding:4px 8px; color:#6b7280;"></th>
                    <th style="text-align:right; padding:4px 8px; color:#6b7280;">Orig. Curr. ({{ $currency }})</th>
                    <th style="text-align:right; padding:4px 8px; color:#6b7280;">US Dollars</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:5px 8px; font-weight:600; color:#1f262a;">Gross Underwritten Premium</td>
                    <td style="padding:5px 8px; text-align:right;">${{ number_format($fts, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right;">${{ number_format($convPremDoc, 2) }}</td>
                </tr>
                @php
                    $grandDeductOrig = $groupedNodes->sum('subtotal_orig');
                    $grandDeductUsd  = $groupedNodes->sum(function ($g) use ($convRatioDoc) {
                        $usd = (float)($g['subtotal_usd'] ?? 0);
                        return $usd != 0 ? $usd : ((float)($g['subtotal_orig'] ?? 0)) * $convRatioDoc;
                    });
                @endphp
                <tr>
                    <td style="padding:5px 8px; font-weight:600; color:#1f262a;">Total Deductions</td>
                    <td style="padding:5px 8px; text-align:right; color:#dc2626;">-${{ number_format($grandDeductOrig, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right; color:#dc2626;">-${{ number_format($grandDeductUsd, 2) }}</td>
                </tr>
                <tr style="border-top:2px solid #1f262a;">
                    <td style="padding:6px 8px; font-weight:700; color:#1f262a;">Net Underwritten Premium</td>
                    <td style="padding:6px 8px; text-align:right; font-weight:700;">${{ number_format($fts - $grandDeductOrig, 2) }}</td>
                    <td style="padding:6px 8px; text-align:right; font-weight:700;">${{ number_format($convPremDoc - $grandDeductUsd, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Installments --}}
        @if ($transactions->isNotEmpty())
        <details style="margin-bottom:12px;">
            <summary style="cursor:pointer; font-weight:600; font-size:12px; color:#db4a2b; padding:4px 0; list-style:none;">
                ▶ Installments ({{ $transactions->count() }})
            </summary>
            @php
                $netOrig = $fts - $grandDeductOrig;
                $grandInstOrig = 0; $grandInstUsd = 0;
            @endphp
            <table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:8px;">
                <thead>
                    <tr style="border-bottom:1px solid #d1cec9;">
                        <th style="text-align:left; padding:3px 6px; color:#6b7280;">#</th>
                        <th style="text-align:right; padding:3px 6px; color:#6b7280;">Proportion</th>
                        <th style="text-align:right; padding:3px 6px; color:#6b7280;">Exch. Rate</th>
                        <th style="text-align:center; padding:3px 6px; color:#6b7280;">Due Date</th>
                        <th style="text-align:right; padding:3px 6px; color:#6b7280;">Orig. Curr.</th>
                        <th style="text-align:right; padding:3px 6px; color:#6b7280;">USD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $txn)
                    @php
                        $prop     = (float) ($txn['proportion'] ?? 0);
                        $rate     = (float) ($txn['exch_rate']  ?? 0);
                        $amtOrig  = $netOrig * $prop;
                        $amtUsd   = $rate > 0 ? $amtOrig / $rate : 0;
                        $grandInstOrig += $amtOrig;
                        $grandInstUsd  += $amtUsd;
                    @endphp
                    <tr style="border-bottom:1px solid #ede9e4;">
                        <td style="padding:3px 6px;">{{ $txn['index'] ?? ($loop->iteration) }}</td>
                        <td style="padding:3px 6px; text-align:right;">{{ number_format($prop * 100, 2) }}%</td>
                        <td style="padding:3px 6px; text-align:right;">{{ number_format($rate, 4) }}</td>
                        <td style="padding:3px 6px; text-align:center;">
                            {{ isset($txn['due_date']) ? Carbon::parse($txn['due_date'])->format('d/m/Y') : '—' }}
                        </td>
                        <td style="padding:3px 6px; text-align:right;">${{ number_format($amtOrig, 2) }}</td>
                        <td style="padding:3px 6px; text-align:right;">${{ number_format($amtUsd, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="border-top:2px solid #1f262a; background:#f1efea;">
                        <td colspan="4" style="padding:4px 6px; text-align:right; font-weight:700;">Total:</td>
                        <td style="padding:4px 6px; text-align:right; font-weight:700;">${{ number_format($grandInstOrig, 2) }}</td>
                        <td style="padding:4px 6px; text-align:right; font-weight:700;">${{ number_format($grandInstUsd, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </details>

        {{-- Transactions Lifecycle --}}
        @if ($nodesFlat->isNotEmpty())
        <details>
            <summary style="cursor:pointer; font-weight:600; font-size:12px; color:#db4a2b; padding:4px 0; list-style:none;">
                ▶ Transactions Lifecycle
            </summary>
            @foreach ($transactions as $tIdx => $txn)
            @php
                $txnId   = $txn['id'] ?? null;
                $pRaw    = (float) ($txn['proportion'] ?? 0);
                $pPct    = ($pRaw > 1 ? $pRaw : $pRaw * 100);
                $txnLogs = $txnId ? collect($logsByTxn[$txnId] ?? []) : collect();
            @endphp
            <div style="margin-top:10px;">
                <div style="font-weight:600; font-size:12px; color:#1f262a; padding:4px 6px; background:#e8e6e1; border-radius:4px;">
                    Installment {{ $txn['index'] ?? ($tIdx + 1) }}
                    <span style="font-weight:400; color:#6b7280;">
                        ({{ number_format($pPct, 2) }}% · Rate: {{ number_format((float)($txn['exch_rate'] ?? 0), 4) }}
                        · Due: {{ isset($txn['due_date']) ? Carbon::parse($txn['due_date'])->format('d/m/Y') : '—' }})
                    </span>
                </div>
                <table style="width:100%; border-collapse:collapse; font-size:11px; margin-top:4px;">
                    <thead>
                        <tr style="border-bottom:1px solid #d1cec9;">
                            <th style="text-align:left; padding:2px 5px; color:#6b7280;">#</th>
                            <th style="text-align:left; padding:2px 5px; color:#6b7280;">Concept</th>
                            <th style="text-align:left; padding:2px 5px; color:#6b7280;">From</th>
                            <th style="text-align:left; padding:2px 5px; color:#6b7280;">To</th>
                            <th style="text-align:right; padding:2px 5px; color:#6b7280;">Gross</th>
                            <th style="text-align:right; padding:2px 5px; color:#6b7280;">Discount</th>
                            <th style="text-align:right; padding:2px 5px; color:#6b7280;">Banking</th>
                            <th style="text-align:right; padding:2px 5px; color:#6b7280;">Net</th>
                            <th style="text-align:center; padding:2px 5px; color:#6b7280;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nodesFlat as $nIdx => $node)
                        @php
                            $nodeIndex = (int) ($node['index'] ?? ($nIdx + 1));
                            $logRow    = $txnId ? ($txnLogs[$nodeIndex] ?? null) : null;
                            $status    = $logRow['status'] ?? 'preview';
                            $statusColors = match (strtolower((string) $status)) {
                                'completed'  => ['#052e16','#86efac'],
                                'in process' => ['#1e3a5f','#93c5fd'],
                                default      => ['#27272a','#9ca3af'],
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #ede9e4;">
                            <td style="padding:2px 5px;">{{ ($tIdx+1) }}.{{ ($nIdx+1) }}</td>
                            <td style="padding:2px 5px;">{{ $node['deduction'] ?? '—' }}</td>
                            <td style="padding:2px 5px;">{{ $node['partner_short'] ?? $node['partner'] ?? '—' }}</td>
                            <td style="padding:2px 5px;">{{ $logRow['to_short'] ?? ($node['partner_short'] ?? '—') }}</td>
                            <td style="padding:2px 5px; text-align:right;">
                                {{ $logRow['gross'] !== null ? '$'.number_format((float)$logRow['gross'],2) : '—' }}
                            </td>
                            <td style="padding:2px 5px; text-align:right;">
                                {{ $logRow['discount'] !== null ? '$'.number_format((float)$logRow['discount'],2) : '—' }}
                            </td>
                            <td style="padding:2px 5px; text-align:right;">
                                {{ $logRow['banking'] !== null ? '$'.number_format((float)$logRow['banking'],2) : '—' }}
                            </td>
                            <td style="padding:2px 5px; text-align:right; font-weight:600;">
                                {{ $logRow['net'] !== null ? '$'.number_format((float)$logRow['net'],2) : '—' }}
                            </td>
                            <td style="padding:2px 5px; text-align:center;">
                                <span style="
                                    display:inline-block; padding:1px 8px; border-radius:9999px; font-size:10px; font-weight:600;
                                    background-color:{{ $statusColors[0] }}; color:{{ $statusColors[1] }};
                                ">{{ $status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </details>
        @endif
        @endif

    </div>
</details>
@empty
<div style="padding:24px; text-align:center; color:#6b7280;">
    No operative documents found for this business.
</div>
@endforelse

{{-- ══ CONSOLIDATED TOTALS ════════════════════════════════════════════════ --}}
@if (count($summaries) > 0)
<div style="background:#1f262a; border-radius:6px; padding:16px 20px; margin-top:8px;">
    <div style="font-size:14px; font-weight:700; color:#db4a2b; margin-bottom:12px;">
        Consolidated Totals — All Documents
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px; color:#f1efea;">
        <colgroup>
            <col style="width:60%;">
            <col style="width:40%;">
        </colgroup>
        <tbody>
            <tr>
                <td style="padding:5px 8px; color:#9ca3af;">Total Gross Underwritten Premium (FTS)</td>
                <td style="padding:5px 8px; text-align:right; font-weight:600;">${{ number_format($totalFts, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:5px 8px; color:#9ca3af;">Total Deductions</td>
                <td style="padding:5px 8px; text-align:right; font-weight:600; color:#fca5a5;">-${{ number_format($totalDeductions, 2) }}</td>
            </tr>
            <tr style="border-top:1px solid #374151;">
                <td style="padding:7px 8px; font-weight:700; color:#f1efea; font-size:14px;">Net Underwritten Premium</td>
                <td style="padding:7px 8px; text-align:right; font-weight:700; font-size:14px; color:#86efac;">${{ number_format($totalNet, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

</div>

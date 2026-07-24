@php
    $totalDeductOrig  = collect($groupedCostNodes ?? [])->sum('subtotal_orig');
    $totalDeductUsd   = collect($groupedCostNodes ?? [])->sum('subtotal_usd');
    $netFts           = ($totalPremiumFts ?? 0) - $totalDeductOrig;
    $netUsd           = ($totalConvertedPremium ?? 0) - $totalDeductUsd;

    $nodesFlat        = collect($groupedCostNodes ?? [])
        ->flatMap(fn ($g) => $g['nodes'] ?? [])
        ->sortBy('index')
        ->values();

    $logsByTxnColl    = collect($logsByTxn ?? []);
    $transactionsColl = collect($transactions ?? []);

    $schemeMetaById = collect($costSchemes ?? [])
        ->mapWithKeys(function ($s) {
            $key = $s['cscheme_id'] ?? $s['id'] ?? null;
            return $key ? [$key => ['label' => $s['id'] ?? '—', 'share' => (float)($s['share'] ?? 0)]] : [];
        });

    $insuredsGrouped = collect($insureds ?? [])
        ->groupBy(fn ($i) => $i['cscheme_id'] ?? $i['cost_scheme_id'] ?? '—');
@endphp

<div id="summary-print-root" style="
    background: #f1efea;
    color: #1f262a;
    padding: 24px;
    font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    font-size: 13px;
    max-height: calc(100vh - 220px);
    overflow: auto;
">

{{-- ══ TOP BAR ══════════════════════════════════════════════════════════════ --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:2px solid #db4a2b; padding-bottom:10px;">
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <span style="font-size:18px; font-weight:700; color:#db4a2b;">{{ $id ?? '—' }}</span>
        <span style="background:#1f262a; color:#f1efea; padding:2px 10px; border-radius:4px; font-size:11px; font-weight:600;">
            {{ $documentType ?? '—' }}
        </span>
        @if (!empty($inceptionDate) && !empty($expirationDate))
        <span style="color:#6b7280; font-size:12px;">
            {{ \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') }}
            →
            {{ \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') }}
            · {{ $coverageDays ?? 0 }} days
        </span>
        @endif
    </div>
    @if (!empty($id))
    <a href="{{ route('operative-doc.overview.pdf', $id) }}" target="_blank" class="no-print"
       style="display:inline-flex; align-items:center; gap:6px; background:#1f262a; color:#f1efea; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap;">
        ↓ Download PDF
    </a>
    @endif
</div>

{{-- ══ FINANCIAL SUMMARY ════════════════════════════════════════════════════ --}}
<div style="background:#1f262a; border-radius:6px; padding:14px 18px; margin-bottom:16px;">
    <div style="font-size:11px; font-weight:700; color:#db4a2b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
        Financial Summary
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:12px; color:#f1efea;">
        <colgroup><col style="width:55%;"><col style="width:22%;"><col style="width:23%;"></colgroup>
        <thead>
            <tr>
                <th style="text-align:left; padding:3px 8px; color:#6b7280; font-weight:400;"></th>
                <th style="text-align:right; padding:3px 8px; color:#9ca3af; font-weight:400; font-size:11px;">
                    Orig. Curr. ({{ $originalCurrency ?? 'USD' }})
                </th>
                <th style="text-align:right; padding:3px 8px; color:#9ca3af; font-weight:400; font-size:11px;">US Dollars</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding:4px 8px; color:#d1d5db;">Gross Underwritten Premium (FTS)</td>
                <td style="padding:4px 8px; text-align:right; font-weight:600;">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                <td style="padding:4px 8px; text-align:right; font-weight:600;">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:4px 8px; color:#d1d5db;">Total Deductions</td>
                <td style="padding:4px 8px; text-align:right; color:#fca5a5;">-${{ number_format($totalDeductOrig, 2) }}</td>
                <td style="padding:4px 8px; text-align:right; color:#fca5a5;">-${{ number_format($totalDeductUsd, 2) }}</td>
            </tr>
            <tr style="border-top:1px solid #374151;">
                <td style="padding:6px 8px; font-weight:700; color:#f1efea; font-size:14px;">Net Underwritten Premium</td>
                <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#86efac;">${{ number_format($netFts, 2) }}</td>
                <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#86efac;">${{ number_format($netUsd, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ══ GENERAL DETAILS ══════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        General Details
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">{{ $premiumType ?? '—' }} · {{ $originalCurrency ?? '—' }}</span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <tbody>
                <tr>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280; width:25%;">Document type:</td>
                    <td style="padding:5px 8px; width:25%; border-bottom:1px solid #e8e6e1;">{{ $documentType ?? '—' }}</td>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280; width:25%;">Creation date:</td>
                    <td style="padding:5px 8px; border-bottom:1px solid #e8e6e1;">
                        {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280;">Premium type:</td>
                    <td style="padding:5px 8px; border-bottom:1px solid #e8e6e1;">{{ $premiumType ?? '—' }}</td>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280;">Period:</td>
                    <td style="padding:5px 8px; border-bottom:1px solid #e8e6e1;">
                        {{ $inceptionDate ? \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') : '—' }}
                        to
                        {{ $expirationDate ? \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280;">Original currency:</td>
                    <td style="padding:5px 8px;">{{ $originalCurrency ?? '—' }}</td>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280;">Coverage days:</td>
                    <td style="padding:5px 8px;">{{ $coverageDays ?? '—' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</details>

{{-- ══ PLACEMENT SCHEMES ════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        Placement Schemes
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">
            {{ count($costSchemes ?? []) }} scheme(s) · Total share: {{ number_format(($totalShare ?? 0) * 100, 2) }}%
        </span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid #d1cec9;">
                    <th style="text-align:left; padding:4px 8px; color:#6b7280; font-weight:600;">#</th>
                    <th style="text-align:left; padding:4px 8px; color:#6b7280; font-weight:600;">ID</th>
                    <th style="text-align:left; padding:4px 8px; color:#6b7280; font-weight:600;">Description</th>
                    <th style="text-align:center; padding:4px 8px; color:#6b7280; font-weight:600;">Share (%)</th>
                    <th style="text-align:center; padding:4px 8px; color:#6b7280; font-weight:600;">Agreement Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($costSchemes ?? [] as $idx => $scheme)
                <tr style="border-bottom:1px solid #ede9e4;">
                    <td style="padding:4px 8px;">{{ $idx + 1 }}</td>
                    <td style="padding:4px 8px; font-family:monospace; font-size:11px;">{{ $scheme['id'] ?? '—' }}</td>
                    <td style="padding:4px 8px;">{{ $scheme['description'] ?? '—' }}</td>
                    <td style="padding:4px 8px; text-align:center; font-weight:600;">{{ number_format(($scheme['share'] ?? 0) * 100, 2) }}%</td>
                    <td style="padding:4px 8px; text-align:center;">{{ $scheme['agreement_type'] ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:10px 8px; text-align:center; color:#9ca3af;">No schemes available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</details>

{{-- ══ INSUREDS ══════════════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        Insureds
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">
            {{ count($insureds ?? []) }} member(s) · Total premium: ${{ number_format(collect($insureds ?? [])->sum('premium'), 2) }}
        </span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7; overflow-x:auto;">
        @forelse ($insuredsGrouped as $schemeId => $rows)
        @php
            $meta        = $schemeMetaById[$schemeId] ?? null;
            $schemeLabel = $meta['label'] ?? $schemeId;
            $schemeShare = (float) ($meta['share'] ?? 0);
            $totalPrem   = $rows->sum('premium');
            $totalFtp    = $rows->sum(fn ($i) => $i['premium_ftp'] ?? 0);
            $totalFts    = $rows->sum(fn ($i) => $i['premium_fts'] ?? 0);
        @endphp
        <div style="font-weight:600; font-size:11px; color:#db4a2b; padding:4px 0 4px; margin-top:8px; border-bottom:1px solid #d1cec9; margin-bottom:4px;">
            Placement Scheme: {{ $schemeLabel }}
            <span style="color:#6b7280; font-weight:400;">({{ number_format($schemeShare * 100, 2) }}%)</span>
        </div>
        <table style="width:100%; min-width:800px; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid #d1cec9;">
                    <th style="text-align:left; padding:3px 6px; color:#6b7280;">#</th>
                    <th style="text-align:left; padding:3px 6px; color:#6b7280;">Insured</th>
                    <th style="text-align:left; padding:3px 6px; color:#6b7280;">Coverage</th>
                    <th style="text-align:center; padding:3px 6px; color:#6b7280;">Share</th>
                    <th style="text-align:center; padding:3px 6px; color:#6b7280;">Country</th>
                    <th style="text-align:center; padding:3px 6px; color:#6b7280;">Allocation</th>
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Annual Premium</th>
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Premium Ftp</th>
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Premium Fts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows->values() as $idx => $insured)
                <tr style="border-bottom:1px solid #ede9e4;">
                    <td style="padding:3px 6px;">{{ $idx + 1 }}</td>
                    <td style="padding:3px 6px; overflow:hidden; text-overflow:ellipsis; max-width:200px;" title="{{ $insured['company']['name'] ?? '—' }}">
                        {{ $insured['company']['name'] ?? '—' }}
                    </td>
                    <td style="padding:3px 6px;">{{ $insured['coverage']['name'] ?? '—' }}</td>
                    <td style="padding:3px 6px; text-align:center;">{{ number_format($schemeShare * 100, 2) }}%</td>
                    <td style="padding:3px 6px; text-align:center;">{{ $insured['company']['country']['name'] ?? '—' }}</td>
                    <td style="padding:3px 6px; text-align:center;">{{ number_format(($insured['allocation_percent'] ?? 0) * 100, 2) }}%</td>
                    <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium'] ?? 0, 2) }}</td>
                    <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium_ftp'] ?? 0, 2) }}</td>
                    <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium_fts'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
                <tr style="background:#f1efea; border-top:1px solid #d1cec9;">
                    <td colspan="5" style="padding:4px 6px;"></td>
                    <td style="padding:4px 6px; text-align:right; font-weight:600; color:#6b7280; font-size:11px;">Totals:</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($totalPrem, 2) }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($totalFtp, 2) }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($totalFts, 2) }}</td>
                </tr>
            </tbody>
        </table>
        @empty
        <div style="padding:10px 0; text-align:center; color:#9ca3af;">No insureds available</div>
        @endforelse
    </div>
</details>

{{-- ══ COSTS BREAKDOWN ══════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        Costs Breakdown
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">Total deductions: -${{ number_format($totalDeductOrig, 2) }}</span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <colgroup>
                <col style="width:4%;"><col style="width:28%;"><col style="width:28%;"><col style="width:10%;"><col style="width:15%;"><col style="width:15%;">
            </colgroup>
            <tbody>
                {{-- Gross row --}}
                <tr>
                    <td colspan="4" style="padding:5px 8px; font-weight:700; text-align:right; color:#1f262a;">Gross Underwritten Premium</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; border-bottom:1px solid #d1cec9;">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; border-bottom:1px solid #d1cec9;">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
                </tr>
                <tr><td colspan="4" style="padding:2px 0;"></td>
                    <td style="text-align:right; padding:2px 8px; color:#6b7280; font-size:11px;">Orig. Curr.</td>
                    <td style="text-align:right; padding:2px 8px; color:#6b7280; font-size:11px;">US Dollars</td>
                </tr>
                <tr>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">#</th>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Partner</th>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Concept</th>
                    <th style="text-align:right; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Value</th>
                    <th style="border-bottom:1px solid #d1cec9;"></th>
                    <th style="border-bottom:1px solid #d1cec9;"></th>
                </tr>

                @forelse ($groupedCostNodes ?? [] as $group)
                    <tr><td colspan="6" style="padding:2px 0;"><div style="border-top:1px dashed #d1cec9;"></div></td></tr>
                    @foreach ($group['nodes'] as $node)
                    <tr style="border-bottom:1px solid #ede9e4;">
                        <td style="padding:3px 8px;">{{ $node['index'] }}</td>
                        <td style="padding:3px 8px;">{{ $node['partner'] ?? '—' }}</td>
                        <td style="padding:3px 8px;">{{ $node['deduction'] ?? '—' }}</td>
                        <td style="padding:3px 8px; text-align:right;">{{ number_format(($node['value'] ?? 0) * 100, 2) }}%</td>
                        <td style="padding:3px 8px; text-align:right; color:#dc2626;">-${{ number_format($node['deduction_amount'] ?? 0, 2) }}</td>
                        <td style="padding:3px 8px; text-align:right; color:#dc2626;">-${{ number_format($node['deduction_usd'] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background:#f1efea;">
                        <td colspan="3" style="padding:3px 8px; text-align:right; color:#6b7280; font-size:11px;">
                            Share {{ number_format(($group['share'] ?? 0) * 100, 2) }}% subtotal:
                        </td>
                        <td></td>
                        <td style="padding:3px 8px; text-align:right; font-weight:600;">-${{ number_format($group['subtotal_orig'] ?? 0, 2) }}</td>
                        <td style="padding:3px 8px; text-align:right; font-weight:600;">-${{ number_format($group['subtotal_usd'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:10px 8px; text-align:center; color:#9ca3af;">No cost nodes available</td></tr>
                @endforelse

                <tr style="border-top:2px solid #1f262a;">
                    <td colspan="3" style="padding:5px 8px; font-weight:700; text-align:right; color:#1f262a;">Total Deductions:</td>
                    <td></td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; color:#dc2626;">-${{ number_format($totalDeductOrig, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; color:#dc2626;">-${{ number_format($totalDeductUsd, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding:6px 8px; font-weight:700; text-align:right; font-size:14px; color:#1f262a;">Net Underwritten Premium:</td>
                    <td></td>
                    <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#166534; border-top:1px solid #d1cec9;">${{ number_format($netFts, 2) }}</td>
                    <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#166534; border-top:1px solid #d1cec9;">${{ number_format($netUsd, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</details>

{{-- ══ TRANSACTIONS ══════════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        Transactions
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">{{ $transactionsColl->count() }} installment(s)</span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7;">
        @php $netPrem = ($totalPremiumFts ?? 0) - $totalDeductOrig; $grandOrig = 0; $grandUsd = 0; @endphp
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="border-bottom:1px solid #d1cec9;">
                    <th style="text-align:left; padding:4px 8px; color:#6b7280;">#</th>
                    <th style="text-align:right; padding:4px 8px; color:#6b7280;">Proportion</th>
                    <th style="text-align:right; padding:4px 8px; color:#6b7280;">Exchange Rate</th>
                    <th style="text-align:center; padding:4px 8px; color:#6b7280;">Due Date</th>
                    <th style="text-align:right; padding:4px 8px; color:#6b7280;">Orig. Curr.</th>
                    <th style="text-align:right; padding:4px 8px; color:#6b7280;">US Dollars</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactionsColl as $txn)
                @php
                    $proportion = floatval($txn['proportion'] ?? 0);
                    $rate       = floatval($txn['exch_rate']  ?? 0);
                    $amtOrig    = $netPrem * $proportion;
                    $amtUsd     = $rate > 0 ? $amtOrig / $rate : 0;
                    $grandOrig += $amtOrig;
                    $grandUsd  += $amtUsd;
                @endphp
                <tr style="border-bottom:1px solid #ede9e4;">
                    <td style="padding:4px 8px;">{{ $loop->iteration }}</td>
                    <td style="padding:4px 8px; text-align:right;">{{ number_format($proportion * 100, 2) }}%</td>
                    <td style="padding:4px 8px; text-align:right;">{{ number_format($rate, 4) }}</td>
                    <td style="padding:4px 8px; text-align:center;">
                        {{ isset($txn['due_date']) ? \Carbon\Carbon::parse($txn['due_date'])->format('d/m/Y') : '—' }}
                    </td>
                    <td style="padding:4px 8px; text-align:right; white-space:nowrap;">${{ number_format($amtOrig, 2) }}</td>
                    <td style="padding:4px 8px; text-align:right; white-space:nowrap;">${{ number_format($amtUsd, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:10px 8px; text-align:center; color:#9ca3af;">No installments available</td></tr>
                @endforelse
                @if ($transactionsColl->isNotEmpty())
                <tr style="border-top:2px solid #1f262a; background:#f1efea;">
                    <td colspan="4" style="padding:5px 8px; text-align:right; font-weight:700; color:#1f262a;">Total:</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($grandOrig, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($grandUsd, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</details>

{{-- ══ TRANSACTIONS LIFECYCLE ════════════════════════════════════════════════ --}}
@if ($transactionsColl->isNotEmpty() && $nodesFlat->isNotEmpty())
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none;">
        Transactions Lifecycle
    </summary>
    <div style="padding:12px 14px; background:#faf9f7;">

        @foreach ($transactionsColl as $tIdx => $txn)
        @php
            $txnId    = $txn['id'] ?? null;
            $pRaw     = (float) ($txn['proportion'] ?? 0);
            $pDec     = $pRaw > 1 ? $pRaw / 100 : $pRaw;
            $pPct     = $pDec * 100;
            $dueDate  = $txn['due_date'] ?? null;
            $exchRate = $txn['exch_rate'] ?? null;
            $txnLogs  = $txnId ? collect($logsByTxnColl[$txnId] ?? []) : collect();
        @endphp

        <div style="background:#e8e6e1; border-radius:4px; padding:6px 10px; margin-top:{{ $tIdx > 0 ? '14px' : '0' }}; margin-bottom:8px; font-weight:600; font-size:12px; color:#1f262a;">
            Installment {{ $txn['index'] ?? ($tIdx + 1) }}
            <span style="font-weight:400; color:#6b7280; font-size:11px; margin-left:8px;">
                Proportion: {{ number_format($pPct, 2) }}% ·
                Exch. Rate: {{ $exchRate !== null ? number_format((float)$exchRate, 4) : '—' }} ·
                Due: {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : '—' }}
            </span>
        </div>

        <table style="width:100%; border-collapse:collapse; font-size:11px;">
            <thead>
                <tr style="border-bottom:1px solid #d1cec9;">
                    <th style="padding:3px 6px; text-align:left; color:#6b7280;">#</th>
                    <th style="padding:3px 6px; text-align:left; color:#6b7280;">Deduction</th>
                    <th style="padding:3px 6px; text-align:left; color:#6b7280;">Source</th>
                    <th style="padding:3px 6px; text-align:left; color:#6b7280;">Destination</th>
                    <th style="padding:3px 6px; text-align:right; color:#6b7280; white-space:nowrap;">Exch. Rate</th>
                    <th style="padding:3px 6px; text-align:right; color:#6b7280; white-space:nowrap;">Gross Amount</th>
                    <th style="padding:3px 6px; text-align:right; color:#6b7280;">Discount</th>
                    <th style="padding:3px 6px; text-align:right; color:#6b7280; white-space:nowrap;">Banking Fee</th>
                    <th style="padding:3px 6px; text-align:right; color:#6b7280; white-space:nowrap;">Net Amount</th>
                    <th style="padding:3px 6px; text-align:center; color:#6b7280;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nodesFlat as $nIdx => $node)
                @php
                    $num       = ($tIdx + 1) . '.' . ($nIdx + 1);
                    $nodeIndex = (int) ($node['index'] ?? ($nIdx + 1));
                    $logRow    = $txnId ? ($txnLogs[$nodeIndex] ?? null) : null;
                    $gross     = $logRow['gross']    ?? null;
                    $discount  = $logRow['discount'] ?? null;
                    $banking   = $logRow['banking']  ?? null;
                    $net       = $logRow['net']      ?? null;
                    $logRate   = $logRow['exch_rate'] ?? ($exchRate !== null ? (float)$exchRate : null);
                    $dest      = $logRow['to_short'] ?? ($node['partner_short'] ?? $node['partner'] ?? '—');
                    $status    = $logRow['status'] ?? 'preview';
                    $statusStyle = match (strtolower((string)$status)) {
                        'completed'  => 'background:#052e16; color:#86efac;',
                        'in process' => 'background:#1e3a5f; color:#93c5fd;',
                        default      => 'background:#27272a; color:#9ca3af;',
                    };
                @endphp
                <tr style="border-bottom:1px solid #ede9e4;">
                    <td style="padding:3px 6px;">{{ $num }}</td>
                    <td style="padding:3px 6px;">{{ $node['deduction'] ?? '—' }}</td>
                    <td style="padding:3px 6px;">{{ $node['partner_short'] ?? $node['partner'] ?? '—' }}</td>
                    <td style="padding:3px 6px;">{{ $dest }}</td>
                    <td style="padding:3px 6px; text-align:right;">
                        {{ $logRate !== null ? number_format((float)$logRate, 5) : '—' }}
                    </td>
                    <td style="padding:3px 6px; text-align:right;">
                        {{ $gross !== null ? number_format((float)$gross, 2) : '—' }}
                    </td>
                    <td style="padding:3px 6px; text-align:right;">
                        {{ $discount !== null ? number_format((float)$discount, 2) : '—' }}
                    </td>
                    <td style="padding:3px 6px; text-align:right;">
                        {{ $banking !== null ? number_format((float)$banking, 2) : '—' }}
                    </td>
                    <td style="padding:3px 6px; text-align:right; font-weight:600;">
                        {{ $net !== null ? number_format((float)$net, 2) : '—' }}
                    </td>
                    <td style="padding:3px 6px; text-align:center;">
                        <span style="display:inline-block; padding:1px 8px; border-radius:9999px; font-size:10px; font-weight:600; {{ $statusStyle }}">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach

    </div>
</details>
@endif

</div>

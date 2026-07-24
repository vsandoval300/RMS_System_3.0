@php
    $grandTotalOrig  = collect($groupedCostNodes ?? [])->sum('subtotal_orig');
    $grandTotalUsd   = collect($groupedCostNodes ?? [])->sum('subtotal_usd');

    $schemeMetaById  = collect($costSchemes ?? [])
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
    <div style="display:flex; align-items:center; gap:10px;">
        @if (!empty($id))
        <a href="{{ route('operative-doc.overview.pdf', $id) }}" target="_blank"
           style="display:inline-flex; align-items:center; gap:6px; background:#1f262a; color:#f1efea; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap;">
            ↓ Download PDF
        </a>
        @endif
        <span style="font-size:11px; color:#9ca3af; font-style:italic;">Live preview</span>
    </div>
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
                <th style="text-align:left; padding:3px 8px;"></th>
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
                <td style="padding:4px 8px; text-align:right; color:#fca5a5;">-${{ number_format($grandTotalOrig, 2) }}</td>
                <td style="padding:4px 8px; text-align:right; color:#fca5a5;">-${{ number_format($grandTotalUsd, 2) }}</td>
            </tr>
            <tr style="border-top:1px solid #374151;">
                <td style="padding:6px 8px; font-weight:700; color:#f1efea; font-size:14px;">Net Underwritten Premium</td>
                <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#86efac;">${{ number_format($netUnderwrittenOrig ?? 0, 2) }}</td>
                <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#86efac;">${{ number_format($netUnderwrittenUsd ?? 0, 2) }}</td>
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
                    <td style="padding:5px 8px; border-bottom:1px solid #e8e6e1;">{{ $originalCurrency ?? '—' }}</td>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280;">Coverage days:</td>
                    <td style="padding:5px 8px; border-bottom:1px solid #e8e6e1;">{{ isset($coverageDays) ? (int)$coverageDays : '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:5px 8px; font-weight:600; color:#6b7280;">RoE for Reference:</td>
                    <td style="padding:5px 8px;" colspan="3">
                        {{ isset($roe_fs) ? number_format((float)$roe_fs, 8) : '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</details>

{{-- ══ PLACEMENT SCHEMES ════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        Placement Schemes
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">{{ count($costSchemes ?? []) }} scheme(s)</span>
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
                @forelse ($costSchemes ?? [] as $index => $scheme)
                <tr style="border-bottom:1px solid #ede9e4;">
                    <td style="padding:4px 8px;">{{ $index + 1 }}</td>
                    <td style="padding:4px 8px; font-family:monospace; font-size:11px;">{{ $scheme['id'] ?? '—' }}</td>
                    <td style="padding:4px 8px;">{{ $scheme['description'] ?? '—' }}</td>
                    <td style="padding:4px 8px; text-align:center; font-weight:600;">{{ number_format(($scheme['share'] ?? 0) * 100, 2) }}%</td>
                    <td style="padding:4px 8px; text-align:center;">{{ $scheme['agreement_type'] ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:10px 8px; text-align:center; color:#9ca3af;">No cost schemes available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</details>

{{-- ══ INSUREDS ══════════════════════════════════════════════════════════════ --}}
<details open style="margin-bottom:10px; border:1px solid #d1cec9; border-radius:6px; overflow:hidden;">
    <summary style="background:#1f262a; color:#f1efea; padding:8px 14px; cursor:pointer; font-weight:600; font-size:12px; list-style:none; display:flex; justify-content:space-between; align-items:center;">
        Insureds
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">{{ count($insureds ?? []) }} member(s)</span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7; overflow-x:auto;">
        @forelse ($insuredsGrouped as $schemeId => $rows)
        @php
            $meta        = $schemeMetaById[$schemeId] ?? null;
            $schemeLabel = $meta['label'] ?? $schemeId;
            $schemeShare = (float)($meta['share'] ?? 0);
            $countInsureds   = $rows->unique(fn($i) => $i['company']['name'] ?? null)->count();
            $totalAllocation = $rows->sum('allocation_percent');
            $totalPremium    = $rows->sum('premium');
            $totalFtp        = $rows->sum(fn($i) => $i['premium_ftp'] ?? 0);
            $totalFts        = $rows->sum(fn($i) => $i['premium_fts'] ?? 0);
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
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Allocation</th>
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Annual Premium</th>
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Premium Ftp</th>
                    <th style="text-align:right; padding:3px 6px; color:#6b7280;">Premium Fts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows->values() as $index => $insured)
                <tr style="border-bottom:1px solid #ede9e4;">
                    <td style="padding:3px 6px;">{{ $index + 1 }}</td>
                    <td style="padding:3px 6px; overflow:hidden; text-overflow:ellipsis; max-width:200px;" title="{{ $insured['company']['name'] ?? '—' }}">
                        {{ $insured['company']['name'] ?? '—' }}
                    </td>
                    <td style="padding:3px 6px; overflow:hidden; text-overflow:ellipsis;" title="{{ $insured['coverage']['name'] ?? '—' }}">
                        {{ $insured['coverage']['name'] ?? '—' }}
                    </td>
                    <td style="padding:3px 6px; text-align:center;">{{ number_format($schemeShare * 100, 2) }}%</td>
                    <td style="padding:3px 6px; text-align:center;">{{ $insured['company']['country']['name'] ?? '—' }}</td>
                    <td style="padding:3px 6px; text-align:right;">{{ isset($insured['allocation_percent']) ? number_format($insured['allocation_percent'] * 100, 2).'%' : '—' }}</td>
                    <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium'] ?? 0, 2) }}</td>
                    <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium_ftp'] ?? 0, 2) }}</td>
                    <td style="padding:3px 6px; text-align:right; white-space:nowrap;">${{ number_format($insured['premium_fts'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
                <tr style="background:#f1efea; border-top:1px solid #d1cec9;">
                    <td style="padding:4px 6px;">{{ $countInsureds }}</td>
                    <td style="padding:4px 6px; font-weight:600; color:#6b7280; font-size:11px;">
                        {{ $countInsureds === 1 ? 'insured' : 'insureds' }}
                    </td>
                    <td colspan="3" style="padding:4px 6px;"></td>
                    <td style="padding:4px 6px; text-align:right; font-weight:600; color:#6b7280; font-size:11px;">Totals:</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:700; white-space:nowrap;">${{ number_format($totalPremium, 2) }}</td>
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
        <span style="color:#9ca3af; font-size:11px; font-weight:400;">Total deductions: -${{ number_format($grandTotalOrig, 2) }}</span>
    </summary>
    <div style="padding:12px 14px; background:#faf9f7;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <colgroup>
                <col style="width:4%;"><col style="width:28%;"><col style="width:9%;"><col style="width:24%;"><col style="width:10%;"><col style="width:12%;"><col style="width:13%;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="5" style="padding:5px 8px; font-weight:700; text-align:right; color:#1f262a;">Gross Underwritten Premium</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; border-bottom:1px solid #d1cec9;">${{ number_format($totalPremiumFts ?? 0, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; border-bottom:1px solid #d1cec9;">${{ number_format($totalConvertedPremium ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" style="padding:2px 0;"></td>
                    <td style="text-align:right; padding:2px 8px; color:#6b7280; font-size:11px;">Orig. Curr.</td>
                    <td style="text-align:right; padding:2px 8px; color:#6b7280; font-size:11px;">US Dollars</td>
                </tr>
                <tr>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">#</th>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Partner</th>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Share</th>
                    <th style="text-align:left; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Concept</th>
                    <th style="text-align:right; padding:3px 8px; color:#6b7280; border-bottom:1px solid #d1cec9;">Value</th>
                    <th style="border-bottom:1px solid #d1cec9;"></th>
                    <th style="border-bottom:1px solid #d1cec9;"></th>
                </tr>

                @forelse ($groupedCostNodes ?? [] as $group)
                    <tr><td colspan="7" style="padding:2px 0;"><div style="border-top:1px dashed #d1cec9;"></div></td></tr>
                    @foreach ($group['nodes'] as $node)
                    <tr style="border-bottom:1px solid #ede9e4;">
                        <td style="padding:3px 8px;">{{ $node['index'] }}</td>
                        <td style="padding:3px 8px;">{{ $node['partner'] ?? '—' }}</td>
                        <td style="padding:3px 8px;">{{ number_format(($node['share'] ?? 0) * 100, 2) }}%</td>
                        <td style="padding:3px 8px;">{{ $node['deduction'] ?? '—' }}</td>
                        <td style="padding:3px 8px; text-align:right;">{{ number_format(($node['value'] ?? 0) * 100, 2) }}%</td>
                        <td style="padding:3px 8px; text-align:right; color:#dc2626;">-${{ number_format(($node['deduction_amount'] ?? 0) * -1, 2) }}</td>
                        <td style="padding:3px 8px; text-align:right; color:#dc2626;">-${{ number_format(($node['deduction_usd'] ?? 0) * -1, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background:#f1efea;">
                        <td colspan="4" style="padding:3px 8px; text-align:right; color:#6b7280; font-size:11px;">
                            Share {{ number_format(($group['share'] ?? 0) * 100, 2) }}% subtotal:
                        </td>
                        <td></td>
                        <td style="padding:3px 8px; text-align:right; font-weight:600;">-${{ number_format(($group['subtotal_orig'] ?? 0) * -1, 2) }}</td>
                        <td style="padding:3px 8px; text-align:right; font-weight:600;">-${{ number_format(($group['subtotal_usd'] ?? 0) * -1, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:10px 8px; text-align:center; color:#9ca3af;">No cost nodes available</td></tr>
                @endforelse

                <tr style="border-top:2px solid #1f262a;">
                    <td colspan="4" style="padding:5px 8px; font-weight:700; text-align:right; color:#1f262a;">Total Deductions:</td>
                    <td></td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; color:#dc2626;">-${{ number_format($grandTotalOrig * -1, 2) }}</td>
                    <td style="padding:5px 8px; text-align:right; font-weight:700; color:#dc2626;">-${{ number_format($grandTotalUsd * -1, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="padding:6px 8px; font-weight:700; text-align:right; font-size:14px; color:#1f262a;">Net Underwritten Premium:</td>
                    <td></td>
                    <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#166534; border-top:1px solid #d1cec9;">${{ number_format($netUnderwrittenOrig ?? 0, 2) }}</td>
                    <td style="padding:6px 8px; text-align:right; font-weight:700; font-size:14px; color:#166534; border-top:1px solid #d1cec9;">${{ number_format($netUnderwrittenUsd ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</details>

</div>

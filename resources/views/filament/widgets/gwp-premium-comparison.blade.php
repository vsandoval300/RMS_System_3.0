@php
    $years      = $this->getAvailableYears();
    $rows       = $this->getData();
    $biz        = $this->getBusinessCounts();
    $cedantFee              = $this->getCedantFee();
    $retrocedantFee         = $this->getRetrocedantFee();
    $retrocedantReferralFee = $this->getRetrocedantReferralFee();
    $managementFee          = $this->getManagementFee();
    $prevYear               = $this->selectedYear - 1;

    $fmt = function (float $n): string {
        $abs = abs($n);
        if ($abs >= 1_000_000) return number_format($n / 1_000_000, 1) . 'M';
        if ($abs >= 1_000)     return number_format($n / 1_000, 1) . 'K';
        return number_format($n, 0);
    };

    // Always express in millions — used for fee tiles so small values show as "0.0M" not bare numbers
    $fmtM = fn (float $n): string => number_format($n / 1_000_000, 1) . 'M';

    $totalAc     = array_sum(array_column($rows, 'ac'));
    $totalPl     = array_sum(array_column($rows, 'pl'));
    $totalDelta  = $totalAc - $totalPl;
    $bizDelta    = $biz['ac'] - $biz['pl'];
    $maxAc       = empty($rows) ? 1 : (max(array_column($rows, 'ac')) ?: 1);

    $withOpAc      = count(array_filter($rows, fn($r) => $r['ac'] > 0));
    $withOpPl      = count(array_filter($rows, fn($r) => $r['pl'] > 0));
    $withoutOpAc   = count(array_filter($rows, fn($r) => $r['ac'] == 0));
    $withoutOpPl   = count(array_filter($rows, fn($r) => $r['pl'] == 0));
    $withOpDelta   = $withOpAc  - $withOpPl;
    $withoutOpDelta= $withoutOpAc - $withoutOpPl;

    $netUnderwrittenPremium = $totalAc - $managementFee;

    // Fee %s of gross premium
    $cedantFeePct              = $totalAc > 0 ? round($cedantFee              / $totalAc * 100, 1) : 0;
    $retrocedantFeePct         = $totalAc > 0 ? round($retrocedantFee         / $totalAc * 100, 1) : 0;
    $retrocedantReferralFeePct = $totalAc > 0 ? round($retrocedantReferralFee / $totalAc * 100, 1) : 0;
    $managementFeePct          = $totalAc > 0 ? round($managementFee          / $totalAc * 100, 1) : 0;
@endphp

<div>

{{-- ══════════════════════════════════════════════════════
     SECTION 1: GWP Distribution — filtros + KPI tiles
     ══════════════════════════════════════════════════════ --}}
<x-filament::section heading="Partner Insights">

    {{-- ── Filter bar ── --}}
    @php
        $selectStyle = "background:light-dark(#ffffff,#1e2533); border:1px solid light-dark(rgba(0,0,0,0.15),rgba(255,255,255,0.15)); border-radius:6px; padding:7px 28px 7px 10px; font-size:0.85rem; color:light-dark(#111827,#f3f4f6); cursor:pointer; appearance:auto;";
        $labelStyle  = "font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);";
        $divStyle    = "width:1px; height:22px; background:light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));";
    @endphp
    <div style="
        display: flex; flex-direction: column; gap: 0.55rem;
        padding: 0.65rem 1rem; margin-bottom: 1.25rem;
        background: light-dark(#f3f4f6, #252d3d);
        border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        border-radius: 10px;
    ">
        {{-- Row 1: FILTERS label + Year --}}
        <div style="display:flex; align-items:center; gap:1rem;">
            <span style="font-size:0.8rem; font-weight:600; color:light-dark(#6b7280,#9ca3af); letter-spacing:0.03em; text-transform:uppercase; white-space:nowrap;">
                Filters
            </span>
            <div style="{{ $divStyle }}"></div>
            <div style="display:flex; align-items:center; gap:0.4rem;">
                <label style="{{ $labelStyle }}">Year:</label>
                <select wire:model.live="selectedYear" style="{{ $selectStyle }}">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected($y === $this->selectedYear)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Row 2: Reinsurer · Retrocedant · Cedant --}}
        <div style="display:flex; align-items:center; gap:1rem; padding-left:0.25rem;">
            <div style="display:flex; align-items:center; gap:0.4rem;">
                <label style="{{ $labelStyle }}">Reinsurer:</label>
                <select wire:model.live="selectedReinsurer" style="{{ $selectStyle }}">
                    <option value="">— All —</option>
                    @foreach ($this->getReinsurers() as $id => $name)
                        <option value="{{ $id }}" @selected($id == $this->selectedReinsurer)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="{{ $divStyle }}"></div>
            <div style="display:flex; align-items:center; gap:0.4rem;">
                <label style="{{ $labelStyle }}">Retrocedant:</label>
                <select wire:model.live="selectedRetrocedant" style="{{ $selectStyle }}">
                    <option value="">— All —</option>
                    @foreach ($this->getRetrocedants() as $id => $name)
                        <option value="{{ $id }}" @selected($id == $this->selectedRetrocedant)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="{{ $divStyle }}"></div>
            <div style="display:flex; align-items:center; gap:0.4rem;">
                <label style="{{ $labelStyle }}">Cedant:</label>
                <select wire:model.live="selectedCedant" style="{{ $selectStyle }}">
                    <option value="">— All —</option>
                    @foreach ($this->getCedants() as $id => $name)
                        <option value="{{ $id }}" @selected($id == $this->selectedCedant)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── KPI tiles ── --}}
    <style>
        .rpc-tiles {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.85rem;
        }
        @media (max-width: 1300px) { .rpc-tiles { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 700px)  { .rpc-tiles { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px)  { .rpc-tiles { grid-template-columns: 1fr; } }
        .rpc-tile {
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px; padding: 1rem 1.25rem 0.85rem;
            position: relative; overflow: hidden;
        }
        .rpc-tile-accent {
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px; border-radius: 12px 12px 0 0;
        }
        .rpc-tile-label {
            font-size: 0.81rem; font-weight: 600; letter-spacing: 0.07em;
            text-transform: uppercase; color: light-dark(#9ca3af, #6b7280);
            margin-bottom: 0.3rem; margin-top: 0.25rem;
            min-height: 2.4em;
        }
        .rpc-tile-value {
            font-size: 1.7rem; font-weight: 700;
            color: light-dark(#111827, #f3f4f6);
            line-height: 1.15; font-variant-numeric: tabular-nums;
        }
        .rpc-tile-delta {
            font-size: 0.84rem; font-weight: 600; margin-top: 0.3rem;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .rpc-tile-context {
            font-size: 0.87rem; color: light-dark(#9ca3af, #6b7280);
            margin-top: 0.45rem; padding-top: 0.45rem;
            border-top: 1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));
            line-height: 1.4;
        }
        .rpc-tile-context b { color: light-dark(#6b7280, #9ca3af); font-weight: 600; }
    </style>

    <div class="rpc-tiles">
        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #3b82f6, #6366f1);"></div>
            <div class="rpc-tile-label">Gross Premium (USD)<br>&nbsp;</div>
            <div class="rpc-tile-value">{{ $fmt($totalAc) }}</div>
            <div class="rpc-tile-delta" style="color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $totalDelta >= 0 ? '▲' : '▼' }}
                {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="rpc-tile-context">
                <b>AC {{ $this->selectedYear }}</b> vs <b>PY {{ $prevYear }}</b> · Total gross written premium
            </div>
        </div>

        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #0891b2, #06b6d4);"></div>
            <div class="rpc-tile-label">Cedant Fee<br>&nbsp;</div>
            <div class="rpc-tile-value">{{ $fmtM($cedantFee) }}</div>
            <div class="rpc-tile-delta" style="color:light-dark(#6b7280,#9ca3af);">
                <span style="font-size:0.82rem; font-weight:600;">{{ number_format($cedantFeePct, 1) }}%</span>
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">of GWP</span>
            </div>
            <div class="rpc-tile-context">
                Fee &amp; Referral deductions applied in <b>{{ $this->selectedYear }}</b>
            </div>
        </div>

        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #ea580c, #f97316);"></div>
            <div class="rpc-tile-label">Retrocedant Fee<br>&nbsp;</div>
            <div class="rpc-tile-value">{{ $fmtM($retrocedantFee) }}</div>
            <div class="rpc-tile-delta" style="color:light-dark(#6b7280,#9ca3af);">
                <span style="font-size:0.82rem; font-weight:600;">{{ number_format($retrocedantFeePct, 1) }}%</span>
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">of GWP</span>
            </div>
            <div class="rpc-tile-context">
                Fee &amp; Referral deductions applied in <b>{{ $this->selectedYear }}</b>
            </div>
        </div>

        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #8b5cf6, #6d28d9);"></div>
            <div class="rpc-tile-label">Retrocedant Referral Fee</div>
            <div class="rpc-tile-value">{{ $fmtM($retrocedantReferralFee) }}</div>
            <div class="rpc-tile-delta" style="color:light-dark(#6b7280,#9ca3af);">
                <span style="font-size:0.82rem; font-weight:600;">{{ number_format($retrocedantReferralFeePct, 1) }}%</span>
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">of GWP</span>
            </div>
            <div class="rpc-tile-context">
                Referral deductions applied in <b>{{ $this->selectedYear }}</b>
            </div>
        </div>

        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="rpc-tile-label">Management Fee<br>&nbsp;</div>
            <div class="rpc-tile-value">{{ $fmtM($managementFee) }}</div>
            <div class="rpc-tile-delta" style="color:light-dark(#6b7280,#9ca3af);">
                <span style="font-size:0.82rem; font-weight:600;">{{ number_format($managementFeePct, 1) }}%</span>
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">of GWP</span>
            </div>
            <div class="rpc-tile-context">
                All deductions applied in <b>{{ $this->selectedYear }}</b>
            </div>
        </div>

        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #0f766e, #14b8a6);"></div>
            <div class="rpc-tile-label">Net Underwritten Premium</div>
            <div class="rpc-tile-value">{{ $fmtM($netUnderwrittenPremium) }}</div>
            <div class="rpc-tile-delta" style="color:light-dark(#6b7280,#9ca3af);">
                <span style="font-size:0.82rem; font-weight:600;">{{ number_format($totalAc > 0 ? round($netUnderwrittenPremium / $totalAc * 100, 1) : 0, 1) }}%</span>
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">of GWP</span>
            </div>
            <div class="rpc-tile-context">
                GWP &minus; all deductions in <b>{{ $this->selectedYear }}</b>
            </div>
        </div>
    </div>

</x-filament::section>

{{-- ══════════════════════════════════════════════════════
     SECTION 2, 3, 4: Tres gráficos en grid horizontal
     ══════════════════════════════════════════════════════ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1rem; align-items:start;">

    {{-- ── Gross Premium by Reinsurer ── --}}
    <x-filament::section heading="Gross Premium by Reinsurer">

        <p style="font-size:0.875rem; color:light-dark(#6b7280,#9ca3af); margin:0 0 1rem;">
            Comparing {{ $this->selectedYear }} (AC) vs {{ $prevYear }} (PY)
        </p>

        @if (empty($rows))
            <div style="text-align:center; padding:3rem; color:light-dark(#9ca3af,#6b7280);">
                No data available for {{ $this->selectedYear }}.
            </div>
        @else
        @php
            $displayRows = collect($rows)->sortByDesc('ac')->values()->all();
            $withOpRows  = array_filter($displayRows, fn($r) => $r['ac'] > 0);
            $withOpCount = count($withOpRows);
            $avgAc       = $withOpCount > 0 ? $totalAc / $withOpCount : 0;
            $avgPct      = $maxAc > 0 ? min(round($avgAc / $maxAc * 100, 1), 100) : 0;
        @endphp
        <div>
            {{-- Column headers --}}
            <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:6px 8px;
                        border-bottom:2px solid light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));
                        font-size:0.875rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">
                <div style="white-space:nowrap;">Reinsurer</div>
                <div style="text-align:right; padding-right:6px; white-space:nowrap;">% GWP</div>
                <div wire:click="setSortColumn('ac')" style="cursor:pointer; user-select:none; padding-left:2px; white-space:nowrap;
                     color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }};">
                    AC ({{ $this->selectedYear }}) ↓
                </div>
            </div>

            {{-- Data rows --}}
            @foreach ($displayRows as $row)
            @php
                $acPct    = $maxAc > 0 ? min(round($row['ac'] / $maxAc * 100, 1), 100) : 0;
                $barInner = $acPct > 25;
            @endphp
            <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:4px 8px; align-items:center;
                        border-bottom:1px solid light-dark(rgba(0,0,0,0.05),rgba(255,255,255,0.05));">

                <div style="font-size:0.875rem; color:light-dark(#111827,#f3f4f6); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $row['name'] }}
                </div>

                <div style="text-align:right; padding-right:6px; font-size:0.875rem; font-weight:600;
                            font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                    {{ $totalAc > 0 ? number_format($row['ac'] / $totalAc * 100, 1) . '%' : '—' }}
                </div>

                <div style="flex:1; height:22px; position:relative; border-radius:3px;">
                    <div style="position:absolute; inset:0; background:light-dark(#e5e7eb,rgba(255,255,255,0.06)); border-radius:3px;"></div>
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; background:#41A2C3; border-radius:3px 0 0 3px; z-index:1;"></div>
                    @if($barInner)
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; display:flex; align-items:center; justify-content:flex-end; padding-right:5px; z-index:2;">
                        <span style="font-size:0.85rem; font-weight:600; color:#ffffff; white-space:nowrap;">{{ $fmt($row['ac']) }}</span>
                    </div>
                    @else
                    <div style="position:absolute; top:0; left:{{ $acPct }}%; bottom:0; display:flex; align-items:center; padding-left:5px; z-index:2;">
                        <span style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6); white-space:nowrap;">{{ $fmt($row['ac']) }}</span>
                    </div>
                    @endif
                    <div style="position:absolute; left:{{ $avgPct }}%; top:-3px; bottom:-3px; border-left:2px dashed #C1121F; z-index:3; pointer-events:none;"></div>
                </div>
            </div>
            @endforeach

            {{-- Avg label --}}
            <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:2px 8px 0;">
                <div></div><div></div>
                <div style="position:relative; height:22px;">
                    <div style="position:absolute; left:{{ $avgPct }}%; transform:translateX(-50%); white-space:nowrap; padding-top:2px;">
                        <span style="display:inline-block; font-size:0.78rem; font-weight:700; color:#ffffff;
                                     background:#ef4444; padding:0.18rem 0.55rem; border-radius:999px; line-height:1;">
                            Avg: {{ $fmt($avgAc) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Total row --}}
            <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:4px 8px;
                        border-top:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08));
                        background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</div>
                <div style="text-align:right; padding-right:6px; font-size:0.875rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">100%</div>
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6); padding-left:2px;">{{ $fmt($totalAc) }}</div>
            </div>
        </div>
        @endif

        <div style="margin-top:1rem; text-align:right; font-size:0.82rem; color:light-dark(#9ca3af,#6b7280);">
            All figures expressed in millions of US dollars
        </div>

    </x-filament::section>

    {{-- ── Columna derecha: dos secciones apiladas ── --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">

        <x-filament::section heading="Gross Premium by Retrocedant">
        @php
            $retroRows  = $this->getRetrocedenteData();
            $retroTotal = array_sum(array_column($retroRows, 'gwp'));
            $retroMax   = empty($retroRows) ? 1 : ((float) $retroRows[0]['gwp'] ?: 1);
            $retroAvg   = count($retroRows) ? $retroTotal / count($retroRows) : 0;
            $retroAvgPct= $retroMax > 0 ? min(round($retroAvg / $retroMax * 100, 1), 100) : 0;
        @endphp

            @if (empty($retroRows))
                <div style="text-align:center; padding:3rem; color:light-dark(#9ca3af,#6b7280);">
                    No data available for {{ $this->selectedYear }}.
                </div>
            @else
            <div>
                {{-- Column headers --}}
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:6px 8px;
                            border-bottom:2px solid light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));
                            font-size:0.875rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">
                    <div style="white-space:nowrap;">Retrocedant</div>
                    <div style="text-align:right; padding-right:6px; white-space:nowrap;">% GWP</div>
                    <div style="padding-left:2px; white-space:nowrap;">
                        AC ({{ $this->selectedYear }})
                    </div>
                </div>

                {{-- Data rows --}}
                @foreach ($retroRows as $row)
                @php
                    $rPct    = $retroMax > 0 ? min(round($row['gwp'] / $retroMax * 100, 1), 100) : 0;
                    $rInner  = $rPct > 25;
                @endphp
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:4px 8px; align-items:center;
                            border-bottom:1px solid light-dark(rgba(0,0,0,0.05),rgba(255,255,255,0.05));">

                    <div style="font-size:0.875rem; color:light-dark(#111827,#f3f4f6); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $row['name'] }}
                    </div>

                    <div style="text-align:right; padding-right:6px; font-size:0.875rem; font-weight:600;
                                font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                        {{ $row['pct'] }}%
                    </div>

                    <div style="flex:1; height:22px; position:relative; border-radius:3px;">
                        <div style="position:absolute; inset:0; background:light-dark(#e5e7eb,rgba(255,255,255,0.06)); border-radius:3px;"></div>
                        <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $rPct }}%; background:#06B6D4; border-radius:3px 0 0 3px; z-index:1;"></div>
                        @if($rInner)
                        <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $rPct }}%; display:flex; align-items:center; justify-content:flex-end; padding-right:5px; z-index:2;">
                            <span style="font-size:0.85rem; font-weight:600; color:#ffffff; white-space:nowrap;">{{ $fmt($row['gwp']) }}</span>
                        </div>
                        @else
                        <div style="position:absolute; top:0; left:{{ $rPct }}%; bottom:0; display:flex; align-items:center; padding-left:5px; z-index:2;">
                            <span style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6); white-space:nowrap;">{{ $fmt($row['gwp']) }}</span>
                        </div>
                        @endif
                        <div style="position:absolute; left:{{ $retroAvgPct }}%; top:-3px; bottom:-3px; border-left:2px dashed #C1121F; z-index:3; pointer-events:none;"></div>
                    </div>
                </div>
                @endforeach

                {{-- Avg label --}}
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:2px 8px 0;">
                    <div></div><div></div>
                    <div style="position:relative; height:22px;">
                        <div style="position:absolute; left:{{ $retroAvgPct }}%; transform:translateX(-50%); white-space:nowrap; padding-top:2px;">
                            <span style="display:inline-block; font-size:0.78rem; font-weight:700; color:#ffffff;
                                         background:#ef4444; padding:0.18rem 0.55rem; border-radius:999px; line-height:1;">
                                Avg: {{ $fmt($retroAvg) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Total row --}}
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:4px 8px;
                            border-top:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08));
                            background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                    <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</div>
                    <div style="text-align:right; padding-right:6px; font-size:0.875rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">100%</div>
                    <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6); padding-left:2px;">{{ $fmt($retroTotal) }}</div>
                </div>
            </div>
            @endif

            <div style="margin-top:1rem; text-align:right; font-size:0.82rem; color:light-dark(#9ca3af,#6b7280);">
                All figures expressed in millions of US dollars
            </div>
        </x-filament::section>

        <x-filament::section heading="Gross Premium by Cedant Company">
        @php
            $cedantRows  = $this->getCedantData();
            $cedantTotal = array_sum(array_column($cedantRows, 'gwp'));
            $cedantMax   = empty($cedantRows) ? 1 : ((float) $cedantRows[0]['gwp'] ?: 1);
            $cedantAvg   = count($cedantRows) ? $cedantTotal / count($cedantRows) : 0;
            $cedantAvgPct= $cedantMax > 0 ? min(round($cedantAvg / $cedantMax * 100, 1), 100) : 0;
        @endphp

            @if (empty($cedantRows))
                <div style="text-align:center; padding:3rem; color:light-dark(#9ca3af,#6b7280);">
                    No data available for {{ $this->selectedYear }}.
                </div>
            @else
            <div>
                {{-- Column headers --}}
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:6px 8px;
                            border-bottom:2px solid light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));
                            font-size:0.875rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">
                    <div style="white-space:nowrap;">Cedant</div>
                    <div style="text-align:right; padding-right:6px; white-space:nowrap;">% GWP</div>
                    <div style="padding-left:2px; white-space:nowrap;">
                        AC ({{ $this->selectedYear }})
                    </div>
                </div>

                {{-- Data rows --}}
                @foreach ($cedantRows as $row)
                @php
                    $cPct   = $cedantMax > 0 ? min(round($row['gwp'] / $cedantMax * 100, 1), 100) : 0;
                    $cInner = $cPct > 25;
                @endphp
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:4px 8px; align-items:center;
                            border-bottom:1px solid light-dark(rgba(0,0,0,0.05),rgba(255,255,255,0.05));">

                    <div style="font-size:0.875rem; color:light-dark(#111827,#f3f4f6); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $row['name'] }}
                    </div>

                    <div style="text-align:right; padding-right:6px; font-size:0.875rem; font-weight:600;
                                font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                        {{ $row['pct'] }}%
                    </div>

                    <div style="flex:1; height:22px; position:relative; border-radius:3px;">
                        <div style="position:absolute; inset:0; background:light-dark(#e5e7eb,rgba(255,255,255,0.06)); border-radius:3px;"></div>
                        <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $cPct }}%; background:#06B6D4; border-radius:3px 0 0 3px; z-index:1;"></div>
                        @if($cInner)
                        <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $cPct }}%; display:flex; align-items:center; justify-content:flex-end; padding-right:5px; z-index:2;">
                            <span style="font-size:0.85rem; font-weight:600; color:#ffffff; white-space:nowrap;">{{ $fmt($row['gwp']) }}</span>
                        </div>
                        @else
                        <div style="position:absolute; top:0; left:{{ $cPct }}%; bottom:0; display:flex; align-items:center; padding-left:5px; z-index:2;">
                            <span style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6); white-space:nowrap;">{{ $fmt($row['gwp']) }}</span>
                        </div>
                        @endif
                        <div style="position:absolute; left:{{ $cedantAvgPct }}%; top:-3px; bottom:-3px; border-left:2px dashed #C1121F; z-index:3; pointer-events:none;"></div>
                    </div>
                </div>
                @endforeach

                {{-- Avg label --}}
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:2px 8px 0;">
                    <div></div><div></div>
                    <div style="position:relative; height:22px;">
                        <div style="position:absolute; left:{{ $cedantAvgPct }}%; transform:translateX(-50%); white-space:nowrap; padding-top:2px;">
                            <span style="display:inline-block; font-size:0.78rem; font-weight:700; color:#ffffff;
                                         background:#ef4444; padding:0.18rem 0.55rem; border-radius:999px; line-height:1;">
                                Avg: {{ $fmt($cedantAvg) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Total row --}}
                <div style="display:grid; grid-template-columns:22% 10% 1fr; gap:8px 0; padding:4px 8px;
                            border-top:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08));
                            background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                    <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</div>
                    <div style="text-align:right; padding-right:6px; font-size:0.875rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">100%</div>
                    <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6); padding-left:2px;">{{ $fmt($cedantTotal) }}</div>
                </div>
            </div>
            @endif

            <div style="margin-top:1rem; text-align:right; font-size:0.82rem; color:light-dark(#9ca3af,#6b7280);">
                All figures expressed in millions of US dollars
            </div>
        </x-filament::section>

    </div>

</div>{{-- /charts grid --}}

</div>{{-- /root --}}

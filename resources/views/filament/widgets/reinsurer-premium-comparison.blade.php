@php
    $years    = $this->getAvailableYears();
    $rows     = $this->getData();
    $biz      = $this->getBusinessCounts();
    $prevYear = $this->selectedYear - 1;

    $fmt = function (float $n): string {
        $abs = abs($n);
        if ($abs >= 1_000_000) return number_format($n / 1_000_000, 1) . 'M';
        if ($abs >= 1_000)     return number_format($n / 1_000, 1) . 'K';
        return number_format($n, 0);
    };

    $totalAc    = array_sum(array_column($rows, 'ac'));
    $totalPl    = array_sum(array_column($rows, 'pl'));
    $totalDelta = $totalAc - $totalPl;
    $bizDelta   = $biz['ac'] - $biz['pl'];
    $maxAc      = empty($rows) ? 1 : (max(array_column($rows, 'ac')) ?: 1);

    $withOpAc      = count(array_filter($rows, fn($r) => $r['ac'] > 0));
    $withOpPl      = count(array_filter($rows, fn($r) => $r['pl'] > 0));
    $withoutOpAc   = count(array_filter($rows, fn($r) => $r['ac'] == 0));
    $withoutOpPl   = count(array_filter($rows, fn($r) => $r['pl'] == 0));
    $withOpDelta   = $withOpAc   - $withOpPl;
    $withoutOpDelta= $withoutOpAc - $withoutOpPl;
@endphp

<x-filament::section heading="Reinsurer Metrics">

    {{-- ── Filter bar ── --}}
    <div style="
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 0.6rem 1rem;
        margin-bottom: 1.25rem;
        background: light-dark(#f3f4f6, #252d3d);
        border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        border-radius: 10px;
        flex-wrap: wrap;
    ">
        <span style="font-size:0.8rem; font-weight:600; color:light-dark(#6b7280,#9ca3af); letter-spacing:0.03em; text-transform:uppercase;">
            Filters
        </span>

        <div style="width:1px; height:18px; background:light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));"></div>

        {{-- Year --}}
        <div style="display:flex; align-items:center; gap:0.4rem;">
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Year:</label>
            <select
                wire:model.live="selectedYear"
                style="
                    background: light-dark(#ffffff, #1e2533);
                    border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                    border-radius: 6px;
                    padding: 4px 28px 4px 10px;
                    font-size: 0.85rem;
                    color: light-dark(#111827, #f3f4f6);
                    cursor: pointer;
                    appearance: auto;
                "
            >
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected($y === $this->selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div style="width:1px; height:18px; background:light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));"></div>

        {{-- Reinsurer --}}
        <div style="display:flex; align-items:center; gap:0.4rem;">
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Reinsurer:</label>
            <select
                wire:model.live="selectedReinsurer"
                style="
                    background: light-dark(#ffffff, #1e2533);
                    border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                    border-radius: 6px;
                    padding: 4px 28px 4px 10px;
                    font-size: 0.85rem;
                    color: light-dark(#111827, #f3f4f6);
                    cursor: pointer;
                    appearance: auto;
                "
            >
                <option value="">— All —</option>
                @foreach ($this->getReinsurers() as $id => $shortName)
                    <option value="{{ $id }}" @selected($id == $this->selectedReinsurer)>{{ $shortName }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── Stat tiles ── --}}
    <style>
        .rpc-tiles {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            margin-bottom: 0.6rem;
        }
        @media (max-width: 800px) { .rpc-tiles { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .rpc-tiles { grid-template-columns: 1fr; } }

        .rpc-tile {
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px;
            padding: 1rem 1.25rem 0.85rem;
            position: relative;
            overflow: hidden;
        }
        .rpc-tile-accent {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 12px 12px 0 0;
        }
        .rpc-tile-label {
            font-size: 0.81rem;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: light-dark(#9ca3af, #6b7280);
            margin-bottom: 0.3rem;
            margin-top: 0.25rem;
        }
        .rpc-tile-value {
            font-size: 1.9rem;
            font-weight: 700;
            color: light-dark(#111827, #f3f4f6);
            line-height: 1.15;
            font-variant-numeric: tabular-nums;
        }
        .rpc-tile-delta {
            font-size: 0.84rem;
            font-weight: 600;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .rpc-tile-context {
            font-size: 0.87rem;
            color: light-dark(#9ca3af, #6b7280);
            margin-top: 0.45rem;
            padding-top: 0.45rem;
            border-top: 1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));
            line-height: 1.4;
        }
        .rpc-tile-context b {
            color: light-dark(#6b7280, #9ca3af);
            font-weight: 600;
        }
    </style>

    <div class="rpc-tiles">

        {{-- Tile: Gross Premium --}}
        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #3b82f6, #6366f1);"></div>
            <div class="rpc-tile-label">Gross Premium (USD)</div>
            <div class="rpc-tile-value">{{ $fmt($totalAc) }}</div>
            <div class="rpc-tile-delta" style="color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $totalDelta >= 0 ? '▲' : '▼' }}
                {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="rpc-tile-context">
                <b>AC {{ $this->selectedYear }}</b> vs <b>PY {{ $prevYear }}</b> · Total gross written premium across all reinsurers
            </div>
        </div>

        {{-- Tile: Businesses --}}
        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="rpc-tile-label">Businesses</div>
            <div class="rpc-tile-value">{{ number_format($biz['ac']) }}</div>
            <div class="rpc-tile-delta" style="color:{{ $bizDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $bizDelta >= 0 ? '▲' : '▼' }}
                {{ $bizDelta >= 0 ? '+' : '' }}{{ number_format($bizDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="rpc-tile-context">
                Underwritten policies bound in <b>{{ $this->selectedYear }}</b> · Prior year: <b>{{ number_format($biz['pl']) }}</b>
            </div>
        </div>

        {{-- Tile: With Operation --}}
        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
            <div class="rpc-tile-label">With Operation</div>
            <div class="rpc-tile-value">{{ number_format($withOpAc) }}</div>
            <div class="rpc-tile-delta" style="color:{{ $withOpDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $withOpDelta >= 0 ? '▲' : '▼' }}
                {{ $withOpDelta >= 0 ? '+' : '' }}{{ number_format($withOpDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="rpc-tile-context">
                Reinsurers with premium &gt; 0 in <b>{{ $this->selectedYear }}</b> · Prior year: <b>{{ number_format($withOpPl) }}</b>
            </div>
        </div>

        {{-- Tile: Without Operation --}}
        <div class="rpc-tile">
            <div class="rpc-tile-accent" style="background: linear-gradient(90deg, #8b5cf6, #6d28d9);"></div>
            <div class="rpc-tile-label">Without Operation</div>
            <div class="rpc-tile-value">{{ number_format($withoutOpAc) }}</div>
            <div class="rpc-tile-delta" style="color:{{ $withoutOpDelta <= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $withoutOpDelta <= 0 ? '▼' : '▲' }}
                {{ $withoutOpDelta >= 0 ? '+' : '' }}{{ number_format($withoutOpDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="rpc-tile-context">
                Reinsurers with no premium in <b>{{ $this->selectedYear }}</b> · Prior year: <b>{{ number_format($withoutOpPl) }}</b>
            </div>
        </div>

    </div>

    {{-- Legend row --}}
    <div style="
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
        padding: 0.45rem 0.25rem;
        margin-bottom: 1rem;
        font-size: 0.82rem;
        color: light-dark(#9ca3af, #6b7280);
    ">
        <span style="font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Legend:</span>
        <span>
            <b style="color:light-dark(#374151,#d1d5db);">AC</b> — Actual / Current year ({{ $this->selectedYear }})
        </span>
        <span>
            <b style="color:light-dark(#374151,#d1d5db);">PY</b> — Prior / Last year ({{ $prevYear }})
        </span>
        <span>
            <b style="color:light-dark(#374151,#d1d5db);">ΔPY</b> — Variance vs prior year (AC − PY)
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.25rem;">
            <span style="color:#65a30d;font-weight:700;">▲</span> Favorable
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.25rem;">
            <span style="color:#dc2626;font-weight:700;">▼</span> Unfavorable
        </span>
    </div>

    {{-- ── Main card ── --}}
    <div style="
        background: light-dark(#ffffff, #1e2533);
        border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        border-radius: 12px;
        padding: 1.5rem;
        font-size: 0.875rem;
    ">
        {{-- Card header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">
                    Gross Premium by Reinsurer
                </h3>
                <p style="font-size:0.875rem; color:light-dark(#6b7280,#9ca3af); margin:0;">
                    Comparing {{ $this->selectedYear }} (AC) vs {{ $prevYear }} (PY)
                </p>
            </div>
            {{-- View toggle --}}
            <div style="display:flex; gap:4px; flex-shrink:0;">
                <button wire:click="$set('chartView','table')" title="Table view" style="
                    display:flex; align-items:center; justify-content:center;
                    width:32px; height:32px; border-radius:6px; border:none; cursor:pointer;
                    font-size:1rem; line-height:1;
                    background:{{ $this->chartView === 'table' ? '#41A2C3' : 'light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08))' }};
                    color:{{ $this->chartView === 'table' ? '#ffffff' : 'light-dark(#6b7280,#9ca3af)' }};
                    transition: background .15s;
                ">⊞</button>
                <button wire:click="$set('chartView','bars')" title="Bar chart view" style="
                    display:flex; align-items:center; justify-content:center;
                    width:32px; height:32px; border-radius:6px; border:none; cursor:pointer;
                    font-size:1rem; line-height:1;
                    background:{{ $this->chartView === 'bars' ? '#41A2C3' : 'light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08))' }};
                    color:{{ $this->chartView === 'bars' ? '#ffffff' : 'light-dark(#6b7280,#9ca3af)' }};
                    transition: background .15s;
                ">≡</button>
            </div>
        </div>

        @if (empty($rows))
            <div style="text-align:center; padding:3rem; color:light-dark(#9ca3af,#6b7280);">
                No data available for {{ $this->selectedYear }}.
            </div>

        @elseif ($this->chartView === 'bars')
        @php
            $displayRows  = collect($rows)->sortByDesc('ac')->values()->all();
            $withOpRows   = array_filter($displayRows, fn($r) => $r['ac'] > 0);
            $withOpCount  = count($withOpRows);
            $avgAc        = $withOpCount > 0 ? $totalAc / $withOpCount : 0;
            $avgPct       = $maxAc > 0 ? min(round($avgAc / $maxAc * 100, 1), 100) : 0;
        @endphp
        {{-- ── Horizontal bar chart view ── --}}
        <div>
            {{-- Column headers at top --}}
            <div style="display:grid; grid-template-columns:22% 1fr 6rem 16% 10%; gap:8px 0; padding:6px 8px;
                        border-bottom:2px solid light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));
                        font-size:0.875rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">
                <div>Reinsurer</div>
                <div wire:click="setSortColumn('ac')" style="cursor:pointer; user-select:none; padding-left:2px;
                     color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }};">
                    AC ({{ $this->selectedYear }}) ↓
                </div>
                <div></div>
                <div style="text-align:center;">ΔPY</div>
                <div style="text-align:right;">ΔPY%</div>
            </div>

            {{-- Data rows --}}
            @foreach ($displayRows as $row)
            @php
                $acPct    = $maxAc > 0 ? min(round($row['ac'] / $maxAc * 100, 1), 100) : 0;
                $isUp     = $row['delta'] >= 0;
                $dColor   = $isUp ? '#65a30d' : '#dc2626';
                $barInner = $acPct > 25;
            @endphp
            <div style="display:grid; grid-template-columns:22% 1fr 6rem 16% 10%; gap:8px 0; padding:4px 8px; align-items:center;
                        border-bottom:1px solid light-dark(rgba(0,0,0,0.05),rgba(255,255,255,0.05));">

                {{-- Name --}}
                <div style="font-size:0.875rem; color:light-dark(#111827,#f3f4f6); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $row['name'] }}
                </div>

                {{-- AC horizontal bar + avg dashed line --}}
                <div style="flex:1; height:22px; position:relative; border-radius:3px;">
                    {{-- Track --}}
                    <div style="position:absolute; inset:0; background:light-dark(#e5e7eb,rgba(255,255,255,0.06)); border-radius:3px;"></div>
                    {{-- Filled bar --}}
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; background:#41A2C3; border-radius:3px 0 0 3px; z-index:1;"></div>
                    {{-- Value: inside bar if wide enough, otherwise just right of bar end --}}
                    @if($barInner)
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; display:flex; align-items:center; justify-content:flex-end; padding-right:5px; z-index:2;">
                        <span style="font-size:0.75rem; font-weight:600; color:#ffffff; white-space:nowrap;">{{ $fmt($row['ac']) }}</span>
                    </div>
                    @else
                    <div style="position:absolute; top:0; left:{{ $acPct }}%; bottom:0; display:flex; align-items:center; padding-left:5px; z-index:2;">
                        <span style="font-size:0.875rem; font-weight:500; color:light-dark(#111827,#f3f4f6); white-space:nowrap;">{{ $fmt($row['ac']) }}</span>
                    </div>
                    @endif
                    {{-- Average dashed vertical line --}}
                    <div style="position:absolute; left:{{ $avgPct }}%; top:-3px; bottom:-3px; border-left:2px dashed #C1121F; z-index:3; pointer-events:none;"></div>
                </div>

                {{-- Spacer --}}
                <div></div>

                {{-- ΔPY bar + value at bar tip --}}
                @php
                    $dHalfPct = min($row['bar_pct'] / 2, 50);
                    $dInside  = $dHalfPct >= 35;
                    $dBarLabel = ($isUp ? '+' : '') . $fmt($row['delta']);
                @endphp
                <div style="position:relative; height:22px;">
                    <div style="position:absolute; left:50%; top:-5px; bottom:-5px; width:1px; background:light-dark(rgba(0,0,0,0.25),rgba(255,255,255,0.25));"></div>
                    @if($isUp)
                    <div style="position:absolute; left:50%; top:4px; bottom:4px; width:{{ $dHalfPct }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                        @if($dInside)
                        <div style="position:absolute; right:4px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dBarLabel }}</div>
                        @endif
                    </div>
                    @if(!$dInside)
                    <div style="position:absolute; left:{{ 50 + $dHalfPct }}%; top:50%; transform:translateY(-50%); padding-left:3px; font-size:0.83rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $dBarLabel }}</div>
                    @endif
                    @else
                    <div style="position:absolute; right:50%; top:4px; bottom:4px; width:{{ $dHalfPct }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                        @if($dInside)
                        <div style="position:absolute; left:4px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dBarLabel }}</div>
                        @endif
                    </div>
                    @if(!$dInside)
                    <div style="position:absolute; right:{{ 50 + $dHalfPct }}%; top:50%; transform:translateY(-50%); padding-right:3px; font-size:0.83rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $dBarLabel }}</div>
                    @endif
                    @endif
                </div>

                {{-- ΔPY% --}}
                <div style="text-align:right; font-size:0.83rem; font-weight:600; color:{{ $dColor }};">
                    {{ $isUp ? '+' : '' }}{{ $row['delta_pct'] }}%
                </div>
            </div>
            @endforeach

            {{-- Avg label row --}}
            <div style="display:grid; grid-template-columns:22% 1fr 6rem 16% 10%; gap:8px 0; padding:2px 8px 0;">
                <div></div>
                <div style="position:relative; height:22px;">
                    <div style="position:absolute; left:{{ $avgPct }}%; transform:translateX(-50%); white-space:nowrap; padding-top:2px;">
                        <span style="
                            display: inline-block;
                            font-size: 0.65rem;
                            font-weight: 700;
                            color: #ffffff;
                            background: #ef4444;
                            padding: 0.18rem 0.45rem;
                            border-radius: 999px;
                            line-height: 1;
                        ">Avg: {{ $fmt($avgAc) }}</span>
                    </div>
                </div>
                <div></div>
                <div></div>
                <div></div>
            </div>

            {{-- Total row --}}
            <div style="display:grid; grid-template-columns:22% 1fr 6rem 16% 10%; gap:8px 0; padding:4px 8px;
                        border-top:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08));
                        background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</div>
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6); padding-left:2px;">{{ $fmt($totalAc) }}</div>
                <div></div>
                <div style="text-align:center; font-size:0.83rem; font-weight:700; color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                    {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}
                </div>
                <div></div>
            </div>

        </div>

        @else
        {{-- ── Table view ── --}}
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12));">
                        <th wire:click="setSortColumn('code')"
                            style="text-align:left; padding:4px 8px; font-weight:700; width:8%; cursor:pointer;
                                   color:{{ $this->sortColumn === 'code' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }}; user-select:none;">
                            Code {{ $this->sortColumn === 'code' ? '↑' : '' }}
                        </th>
                        <th style="text-align:left; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:18%;">Reinsurer</th>
                        <th wire:click="setSortColumn('ac')"
                            style="text-align:right; padding:4px 8px; font-weight:700; width:12%; cursor:pointer;
                                   color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }}; user-select:none;">
                            AC <span style="font-weight:400; font-size:0.85rem;">({{ $this->selectedYear }})</span>
                            {{ $this->sortColumn === 'ac' ? '↓' : '' }}
                        </th>
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:12%;">
                            PY <span style="font-weight:400; font-size:0.85rem;">({{ $prevYear }})</span>
                        </th>
                        <th style="width:6rem; padding:0;"></th>
                        <th style="text-align:center; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6);">ΔPY</th>
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:8%;">% AC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr style="border-bottom:1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));">
                        <td style="padding:4px 8px; color:light-dark(#6b7280,#9ca3af); font-size:0.8rem; font-variant-numeric:tabular-nums;">
                            {{ $row['cns_code'] }}
                        </td>
                        <td style="padding:4px 8px; color:light-dark(#111827,#f3f4f6);">
                            {{ $row['name'] }}
                        </td>
                        <td style="padding:4px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">
                            {{ $fmt($row['ac']) }}
                        </td>
                        <td style="padding:4px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $fmt($row['pl']) }}
                        </td>
                        <td style="width:6rem; padding:0;"></td>
                        <td style="padding:4px 8px;">
                            @php
                                $halfPct = min($row['bar_pct'] / 2, 50);
                                $dIsPos  = $row['delta'] >= 0;
                                $dColor  = $dIsPos ? '#65a30d' : '#dc2626';
                                $dLabel  = ($dIsPos ? '+' : '') . $fmt($row['delta']);
                                $inside  = $halfPct >= 35;
                            @endphp
                            <div style="position:relative; height:20px;">
                                {{-- Center line extended to bridge row padding gaps --}}
                                <div style="position:absolute; left:50%; top:-4px; bottom:-4px; width:1px; background:light-dark(rgba(0,0,0,0.25),rgba(255,255,255,0.25));"></div>

                                @if($dIsPos)
                                {{-- Green bar extending right --}}
                                <div style="position:absolute; left:50%; top:3px; bottom:3px; width:{{ $halfPct }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                                    @if($inside)
                                    <div style="position:absolute; right:5px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dLabel }}</div>
                                    @endif
                                </div>
                                @if(!$inside)
                                <div style="position:absolute; left:{{ 50 + $halfPct }}%; top:50%; transform:translateY(-50%); padding-left:4px; font-size:0.83rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $dLabel }}</div>
                                @endif
                                @else
                                {{-- Red bar extending left --}}
                                <div style="position:absolute; right:50%; top:3px; bottom:3px; width:{{ $halfPct }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                                    @if($inside)
                                    <div style="position:absolute; left:5px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dLabel }}</div>
                                    @endif
                                </div>
                                @if(!$inside)
                                <div style="position:absolute; right:{{ 50 + $halfPct }}%; top:50%; transform:translateY(-50%); padding-right:4px; font-size:0.83rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $dLabel }}</div>
                                @endif
                                @endif
                            </div>
                        </td>
                        <td style="padding:4px 8px; text-align:right; font-size:0.83rem; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $totalAc > 0 ? number_format($row['ac'] / $totalAc * 100, 1) . '%' : '—' }}
                        </td>
                    </tr>
                    @endforeach

                    {{-- Totals --}}
                    <tr style="border-top:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12)); background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                        <td style="padding:5px 8px;"></td>
                        <td style="padding:5px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</td>
                        <td style="padding:5px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">{{ $fmt($totalAc) }}</td>
                        <td style="padding:5px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">{{ $fmt($totalPl) }}</td>
                        <td style="width:6rem; padding:0;"></td>
                        <td style="padding:5px 8px; text-align:center; font-size:0.83rem; font-weight:700; font-variant-numeric:tabular-nums; color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                            {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}
                        </td>
                        <td style="padding:5px 8px; text-align:right; font-size:0.83rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">
                            100%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        {{-- Footer --}}
        <div style="margin-top:1rem; text-align:right; font-size:0.82rem; color:light-dark(#9ca3af,#6b7280);">
            All figures expressed in millions of US dollars
        </div>
    </div>


</x-filament::section>

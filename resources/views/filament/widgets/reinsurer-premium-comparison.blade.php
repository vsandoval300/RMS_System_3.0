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

<div>

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
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#374151,#d1d5db);">Year:</label>
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
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#374151,#d1d5db);">Reinsurer:</label>
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
    <div style="display:flex; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap;">

        {{-- Tile: Gross Premium --}}
        <div style="
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px;
            padding: 1.1rem 1.5rem;
            min-width: 180px;
            text-align: center;
        ">
            <div style="font-size:0.95rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); margin-bottom:6px;">
                Gross Premium (USD)
            </div>
            <div style="font-size:1.9rem; font-weight:700; color:light-dark(#111827,#f3f4f6); line-height:1.1;">
                {{ $fmt($totalAc) }}
            </div>
            <div style="font-size:0.9rem; font-weight:600; margin-top:6px; color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}&nbsp;<span style="font-weight:400; opacity:0.8;">ΔPL</span>
            </div>
        </div>

        {{-- Tile: Businesses --}}
        <div style="
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px;
            padding: 1.1rem 1.5rem;
            min-width: 180px;
            text-align: center;
        ">
            <div style="font-size:0.95rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); margin-bottom:6px;">
                Businesses
            </div>
            <div style="font-size:1.9rem; font-weight:700; color:light-dark(#111827,#f3f4f6); line-height:1.1;">
                {{ number_format($biz['ac']) }}
            </div>
            <div style="font-size:0.9rem; font-weight:600; margin-top:6px; color:{{ $bizDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $bizDelta >= 0 ? '+' : '' }}{{ number_format($bizDelta) }}&nbsp;<span style="font-weight:400; opacity:0.8;">ΔPL</span>
            </div>
        </div>

        {{-- Tile: With Operation --}}
        <div style="
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px;
            padding: 1.1rem 1.5rem;
            min-width: 180px;
            text-align: center;
        ">
            <div style="font-size:0.95rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); margin-bottom:6px;">
                With Operation
            </div>
            <div style="font-size:1.9rem; font-weight:700; color:light-dark(#111827,#f3f4f6); line-height:1.1;">
                {{ number_format($withOpAc) }}
            </div>
            <div style="font-size:0.9rem; font-weight:600; margin-top:6px; color:{{ $withOpDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $withOpDelta >= 0 ? '+' : '' }}{{ number_format($withOpDelta) }}&nbsp;<span style="font-weight:400; opacity:0.8;">ΔPL</span>
            </div>
        </div>

        {{-- Tile: Without Operation --}}
        <div style="
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px;
            padding: 1.1rem 1.5rem;
            min-width: 180px;
            text-align: center;
        ">
            <div style="font-size:0.95rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); margin-bottom:6px;">
                Without Operation
            </div>
            <div style="font-size:1.9rem; font-weight:700; color:light-dark(#111827,#f3f4f6); line-height:1.1;">
                {{ number_format($withoutOpAc) }}
            </div>
            <div style="font-size:0.9rem; font-weight:600; margin-top:6px; color:{{ $withoutOpDelta <= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $withoutOpDelta >= 0 ? '+' : '' }}{{ number_format($withoutOpDelta) }}&nbsp;<span style="font-weight:400; opacity:0.8;">ΔPL</span>
            </div>
        </div>

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
                <p style="font-size:0.78rem; color:light-dark(#6b7280,#9ca3af); margin:0;">
                    Comparing {{ $this->selectedYear }} (AC) vs {{ $prevYear }} (PL)
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
            <div style="display:grid; grid-template-columns:22% 1fr 18% 12%; gap:8px; padding:6px 8px;
                        border-bottom:2px solid light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));
                        font-size:0.75rem; font-weight:600; color:light-dark(#6b7280,#9ca3af);">
                <div>Reinsurer</div>
                <div wire:click="setSortColumn('ac')" style="cursor:pointer; user-select:none; padding-left:2px;
                     color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#6b7280,#9ca3af)' }};">
                    AC ({{ $this->selectedYear }}) ↓
                </div>
                <div style="text-align:right;">ΔPL</div>
                <div style="text-align:right;">ΔPL%</div>
            </div>

            {{-- Data rows --}}
            @foreach ($displayRows as $row)
            @php
                $acPct    = $maxAc > 0 ? min(round($row['ac'] / $maxAc * 100, 1), 100) : 0;
                $isUp     = $row['delta'] >= 0;
                $dColor   = $isUp ? '#65a30d' : '#dc2626';
                $barInner = $acPct > 25;
            @endphp
            <div style="display:grid; grid-template-columns:22% 1fr 18% 12%; gap:8px; padding:5px 8px; align-items:center;
                        border-bottom:1px solid light-dark(rgba(0,0,0,0.05),rgba(255,255,255,0.05));">

                {{-- Name --}}
                <div style="font-size:0.8rem; color:light-dark(#111827,#f3f4f6); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $row['name'] }}
                </div>

                {{-- AC horizontal bar + avg dashed line --}}
                <div style="flex:1; height:22px; position:relative; border-radius:3px;">
                    {{-- Track --}}
                    <div style="position:absolute; inset:0; background:light-dark(#e5e7eb,rgba(255,255,255,0.06)); border-radius:3px;"></div>
                    {{-- Filled bar --}}
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; background:#219EBC; border-radius:3px 0 0 3px; z-index:1;"></div>
                    {{-- Value: inside bar if wide enough, otherwise just right of bar end --}}
                    @if($barInner)
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; display:flex; align-items:center; justify-content:flex-end; padding-right:5px; z-index:2;">
                        <span style="font-size:0.7rem; font-weight:600; color:#ffffff; white-space:nowrap;">{{ $fmt($row['ac']) }}</span>
                    </div>
                    @else
                    <div style="position:absolute; top:0; left:{{ $acPct }}%; bottom:0; display:flex; align-items:center; padding-left:5px; z-index:2;">
                        <span style="font-size:0.7rem; font-weight:500; color:light-dark(#374151,#d1d5db); white-space:nowrap;">{{ $fmt($row['ac']) }}</span>
                    </div>
                    @endif
                    {{-- Average dashed vertical line --}}
                    <div style="position:absolute; left:{{ $avgPct }}%; top:-3px; bottom:-3px; border-left:2px dashed #C1121F; z-index:3; pointer-events:none;"></div>
                </div>

                {{-- ΔPL bar + value --}}
                <div style="display:flex; align-items:center; gap:4px;">
                    <div style="flex:1; height:14px; position:relative;">
                        <div style="position:absolute; left:50%; top:0; bottom:0; width:1px; background:light-dark(rgba(0,0,0,0.15),rgba(255,255,255,0.15));"></div>
                        @if($isUp)
                        <div style="position:absolute; left:50%; top:0; bottom:0; width:{{ min($row['bar_pct']/2,50) }}%; background:#65a30d; border-radius:0 2px 2px 0;"></div>
                        @else
                        <div style="position:absolute; right:50%; top:0; bottom:0; width:{{ min($row['bar_pct']/2,50) }}%; background:#dc2626; border-radius:2px 0 0 2px;"></div>
                        @endif
                    </div>
                    <div style="font-size:0.72rem; font-weight:500; color:{{ $dColor }}; white-space:nowrap; min-width:48px; text-align:right;">
                        {{ $isUp ? '+' : '' }}{{ $fmt($row['delta']) }}
                    </div>
                </div>

                {{-- ΔPL% --}}
                <div style="text-align:right; font-size:0.75rem; font-weight:600; color:{{ $dColor }};">
                    {{ $isUp ? '+' : '' }}{{ $row['delta_pct'] }}%
                </div>
            </div>
            @endforeach

            {{-- Avg label row --}}
            <div style="display:grid; grid-template-columns:22% 1fr 18% 12%; gap:8px; padding:2px 8px 0;">
                <div></div>
                <div style="position:relative; height:18px;">
                    <div style="position:absolute; left:{{ $avgPct }}%; transform:translateX(-50%); white-space:nowrap;
                                font-size:0.68rem; font-weight:600; color:#C1121F; padding-top:2px;">
                        Avg. {{ $fmt($avgAc) }}
                    </div>
                </div>
                <div></div>
                <div></div>
            </div>

            {{-- Total row --}}
            <div style="display:grid; grid-template-columns:22% 1fr 18% 12%; gap:8px; padding:7px 8px;
                        border-top:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08));
                        background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</div>
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6); padding-left:2px;">{{ $fmt($totalAc) }}</div>
                <div style="font-size:0.82rem; font-weight:700; text-align:right; color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};">
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
                            style="text-align:left; padding:6px 8px; font-weight:600; width:8%; cursor:pointer;
                                   color:{{ $this->sortColumn === 'code' ? '#41A2C3' : 'light-dark(#374151,#d1d5db)' }}; user-select:none;">
                            Code {{ $this->sortColumn === 'code' ? '↑' : '' }}
                        </th>
                        <th style="text-align:left; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:18%;">Reinsurer</th>
                        <th wire:click="setSortColumn('ac')"
                            style="text-align:right; padding:6px 8px; font-weight:600; width:12%; cursor:pointer;
                                   color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#374151,#d1d5db)' }}; user-select:none;">
                            AC <span style="font-weight:400; font-size:0.85rem;">({{ $this->selectedYear }})</span>
                            {{ $this->sortColumn === 'ac' ? '↓' : '' }}
                        </th>
                        <th style="text-align:right; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:12%;">
                            PL <span style="font-weight:400; font-size:0.85rem;">({{ $prevYear }})</span>
                        </th>
                        <th style="text-align:center; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db);">ΔPL</th>
                        <th style="text-align:right; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:8%;">% AC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    <tr style="border-bottom:1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));">
                        <td style="padding:7px 8px; color:light-dark(#6b7280,#9ca3af); font-size:0.8rem; font-variant-numeric:tabular-nums;">
                            {{ $row['cns_code'] }}
                        </td>
                        <td style="padding:7px 8px; color:light-dark(#111827,#f3f4f6);">
                            {{ $row['name'] }}
                        </td>
                        <td style="padding:7px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">
                            {{ $fmt($row['ac']) }}
                        </td>
                        <td style="padding:7px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $fmt($row['pl']) }}
                        </td>
                        <td style="padding:7px 8px;">
                            @php
                                $halfPct = min($row['bar_pct'] / 2, 50);
                                $dIsPos  = $row['delta'] >= 0;
                                $dColor  = $dIsPos ? '#65a30d' : '#dc2626';
                                $dLabel  = ($dIsPos ? '+' : '') . $fmt($row['delta']);
                                $inside  = $halfPct >= 35;
                            @endphp
                            <div style="position:relative; height:26px;">
                                {{-- Center line --}}
                                <div style="position:absolute; left:50%; top:0; bottom:0; width:1px; background:light-dark(rgba(0,0,0,0.2),rgba(255,255,255,0.2));"></div>

                                @if($dIsPos)
                                {{-- Green bar extending right --}}
                                <div style="position:absolute; left:50%; top:6px; bottom:6px; width:{{ $halfPct }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                                    @if($inside)
                                    <div style="position:absolute; right:5px; top:50%; transform:translateY(-50%); font-size:0.75rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dLabel }}</div>
                                    @endif
                                </div>
                                @if(!$inside)
                                <div style="position:absolute; left:{{ 50 + $halfPct }}%; top:50%; transform:translateY(-50%); padding-left:4px; font-size:0.78rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $dLabel }}</div>
                                @endif
                                @else
                                {{-- Red bar extending left --}}
                                <div style="position:absolute; right:50%; top:6px; bottom:6px; width:{{ $halfPct }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                                    @if($inside)
                                    <div style="position:absolute; left:5px; top:50%; transform:translateY(-50%); font-size:0.75rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dLabel }}</div>
                                    @endif
                                </div>
                                @if(!$inside)
                                <div style="position:absolute; right:{{ 50 + $halfPct }}%; top:50%; transform:translateY(-50%); padding-right:4px; font-size:0.78rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $dLabel }}</div>
                                @endif
                                @endif
                            </div>
                        </td>
                        <td style="padding:7px 8px; text-align:right; font-size:0.8rem; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $totalAc > 0 ? number_format($row['ac'] / $totalAc * 100, 1) . '%' : '—' }}
                        </td>
                    </tr>
                    @endforeach

                    {{-- Totals --}}
                    <tr style="border-top:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12)); background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                        <td style="padding:8px 8px;"></td>
                        <td style="padding:8px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</td>
                        <td style="padding:8px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">{{ $fmt($totalAc) }}</td>
                        <td style="padding:8px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">{{ $fmt($totalPl) }}</td>
                        <td style="padding:8px 8px;">
                            <div style="position:relative; height:26px;">
                                <div style="position:absolute; left:50%; top:0; bottom:0; width:1px; background:light-dark(rgba(0,0,0,0.2),rgba(255,255,255,0.2));"></div>
                                <div style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); font-size:0.85rem; font-weight:700; font-variant-numeric:tabular-nums; color:{{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }}; white-space:nowrap; background:light-dark(rgba(255,255,255,0.9),rgba(30,37,51,0.9)); padding:0 5px; border-radius:3px;">
                                    {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}
                                </div>
                            </div>
                        </td>
                        <td style="padding:8px 8px; text-align:right; font-size:0.82rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">
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

</div>

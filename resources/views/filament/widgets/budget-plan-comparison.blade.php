@php
    $years      = $this->getAvailableYears();
    $rows       = $this->getData();
    $biz        = $this->getBusinessCounts();
    $budgets    = $this->getAvailableBudgets();
    $prevYear   = $this->selectedYear - 1;
    $showPlan   = $this->showPlan;

    $fmt = function (float $n): string {
        $abs = abs($n);
        if ($abs >= 1_000_000) return number_format($n / 1_000_000, 1) . 'M';
        if ($abs >= 1_000)     return number_format($n / 1_000, 1) . 'K';
        return number_format($n, 0);
    };

    $totalAc       = array_sum(array_column($rows, 'ac'));
    $totalPl       = array_sum(array_column($rows, 'pl'));
    $totalPlan     = $showPlan ? array_sum(array_column($rows, 'plan')) : 0;
    $totalDeltaPy  = $totalAc - $totalPl;
    $totalDeltaPln = $totalAc - $totalPlan;
    $bizDelta      = $biz['ac'] - $biz['pl'];
    $maxAc         = empty($rows) ? 1 : (max(array_column($rows, 'ac')) ?: 1);

    $withOpAc      = count(array_filter($rows, fn($r) => $r['ac'] > 0));
    $withOpPl      = count(array_filter($rows, fn($r) => $r['pl'] > 0));
    $withoutOpAc   = count(array_filter($rows, fn($r) => $r['ac'] == 0));
    $withoutOpPl   = count(array_filter($rows, fn($r) => $r['pl'] == 0));
    $withOpDelta   = $withOpAc - $withOpPl;
    $withoutOpDelta = $withoutOpAc - $withoutOpPl;
@endphp

<div>
<x-filament::section heading="Portfolio Metrics">

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
            <select wire:model.live="selectedYear" style="
                background: light-dark(#ffffff, #1e2533);
                border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                border-radius: 6px; padding: 4px 28px 4px 10px;
                font-size: 0.85rem; color: light-dark(#111827, #f3f4f6);
                cursor: pointer; appearance: auto;
            ">
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected($y === $this->selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div style="width:1px; height:18px; background:light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));"></div>

        {{-- Reinsurer --}}
        <div style="display:flex; align-items:center; gap:0.4rem;">
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Reinsurer:</label>
            <select wire:model.live="selectedReinsurer" style="
                background: light-dark(#ffffff, #1e2533);
                border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                border-radius: 6px; padding: 4px 28px 4px 10px;
                font-size: 0.85rem; color: light-dark(#111827, #f3f4f6);
                cursor: pointer; appearance: auto;
            ">
                <option value="">— All —</option>
                @foreach ($this->getReinsurers() as $id => $name)
                    <option value="{{ $id }}" @selected($id == $this->selectedReinsurer)>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div style="width:1px; height:18px; background:light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));"></div>

        {{-- Show Plan toggle --}}
        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; user-select:none;">
            <input
                type="checkbox"
                wire:model.live="showPlan"
                style="width:15px; height:15px; accent-color:#8b5cf6; cursor:pointer;"
            >
            <span style="font-size:0.9rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Show Plan</span>
        </label>

        {{-- Budget selector (only when showPlan) --}}
        @if($showPlan)
        <div style="display:flex; align-items:center; gap:0.4rem;">
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Budget:</label>
            @if(empty($budgets))
                <span style="font-size:0.85rem; color:light-dark(#9ca3af,#6b7280); font-style:italic;">
                    No budgets for {{ $this->selectedYear }}
                </span>
            @else
                <select wire:model.live="selectedBudgetId" style="
                    background: light-dark(#ffffff, #1e2533);
                    border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                    border-radius: 6px; padding: 4px 28px 4px 10px;
                    font-size: 0.85rem; color: light-dark(#111827, #f3f4f6);
                    cursor: pointer; appearance: auto;
                ">
                    <option value="">— Select budget —</option>
                    @foreach ($budgets as $id => $label)
                        <option value="{{ $id }}" @selected($id == $this->selectedBudgetId)>{{ $label }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        @endif

    </div>

    {{-- ── Stat tiles ── --}}
    <style>
        .bpc-tiles {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            margin-bottom: 0.6rem;
        }
        @media (max-width: 800px) { .bpc-tiles { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .bpc-tiles { grid-template-columns: 1fr; } }

        .bpc-tile {
            background: light-dark(#ffffff, #1e2533);
            border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
            border-radius: 12px;
            padding: 1rem 1.25rem 0.85rem;
            position: relative; overflow: hidden;
        }
        .bpc-tile-accent {
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px; border-radius: 12px 12px 0 0;
        }
        .bpc-tile-label {
            font-size: 0.81rem; font-weight: 600; letter-spacing: 0.07em;
            text-transform: uppercase; color: light-dark(#9ca3af, #6b7280);
            margin-bottom: 0.3rem; margin-top: 0.25rem;
        }
        .bpc-tile-value {
            font-size: 1.9rem; font-weight: 700;
            color: light-dark(#111827, #f3f4f6);
            line-height: 1.15; font-variant-numeric: tabular-nums;
        }
        .bpc-tile-delta {
            font-size: 0.84rem; font-weight: 600; margin-top: 0.3rem;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .bpc-tile-context {
            font-size: 0.87rem; color: light-dark(#9ca3af, #6b7280);
            margin-top: 0.45rem; padding-top: 0.45rem;
            border-top: 1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));
            line-height: 1.4;
        }
        .bpc-tile-context b { color: light-dark(#6b7280, #9ca3af); font-weight: 600; }
        .bpc-plan-delta {
            font-size: 0.82rem; font-weight: 600; margin-top: 0.2rem;
            display: flex; align-items: center; gap: 0.3rem;
            padding-top: 0.2rem;
            border-top: 1px dashed light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        }
    </style>

    <div class="bpc-tiles">

        {{-- Tile: Gross Premium --}}
        <div class="bpc-tile">
            <div class="bpc-tile-accent" style="background: linear-gradient(90deg, #3b82f6, #6366f1);"></div>
            <div class="bpc-tile-label">Gross Premium (USD)</div>
            <div class="bpc-tile-value">{{ $fmt($totalAc) }}</div>
            <div class="bpc-tile-delta" style="color:{{ $totalDeltaPy >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $totalDeltaPy >= 0 ? '▲' : '▼' }}
                {{ $totalDeltaPy >= 0 ? '+' : '' }}{{ $fmt($totalDeltaPy) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            @if($showPlan && $totalPlan > 0)
            <div class="bpc-plan-delta" style="color:{{ $totalDeltaPln >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $totalDeltaPln >= 0 ? '▲' : '▼' }}
                {{ $totalDeltaPln >= 0 ? '+' : '' }}{{ $fmt($totalDeltaPln) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPL</span>
            </div>
            @endif
            <div class="bpc-tile-context">
                <b>AC {{ $this->selectedYear }}</b> vs <b>PY {{ $prevYear }}</b>
                @if($showPlan && $totalPlan > 0)
                · Plan: <b>{{ $fmt($totalPlan) }}</b>
                @endif
            </div>
        </div>

        {{-- Tile: Businesses --}}
        <div class="bpc-tile">
            <div class="bpc-tile-accent" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="bpc-tile-label">Businesses</div>
            <div class="bpc-tile-value">{{ number_format($biz['ac']) }}</div>
            <div class="bpc-tile-delta" style="color:{{ $bizDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $bizDelta >= 0 ? '▲' : '▼' }}
                {{ $bizDelta >= 0 ? '+' : '' }}{{ number_format($bizDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="bpc-tile-context">
                Underwritten policies bound in <b>{{ $this->selectedYear }}</b> · Prior year: <b>{{ number_format($biz['pl']) }}</b>
            </div>
        </div>

        {{-- Tile: With Operation --}}
        <div class="bpc-tile">
            <div class="bpc-tile-accent" style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
            <div class="bpc-tile-label">With Operation</div>
            <div class="bpc-tile-value">{{ number_format($withOpAc) }}</div>
            <div class="bpc-tile-delta" style="color:{{ $withOpDelta >= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $withOpDelta >= 0 ? '▲' : '▼' }}
                {{ $withOpDelta >= 0 ? '+' : '' }}{{ number_format($withOpDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="bpc-tile-context">
                Reinsurers with premium &gt; 0 in <b>{{ $this->selectedYear }}</b> · Prior year: <b>{{ number_format($withOpPl) }}</b>
            </div>
        </div>

        {{-- Tile: Without Operation --}}
        <div class="bpc-tile">
            <div class="bpc-tile-accent" style="background: linear-gradient(90deg, #8b5cf6, #6d28d9);"></div>
            <div class="bpc-tile-label">Without Operation</div>
            <div class="bpc-tile-value">{{ number_format($withoutOpAc) }}</div>
            <div class="bpc-tile-delta" style="color:{{ $withoutOpDelta <= 0 ? '#65a30d' : '#dc2626' }};">
                {{ $withoutOpDelta <= 0 ? '▼' : '▲' }}
                {{ $withoutOpDelta >= 0 ? '+' : '' }}{{ number_format($withoutOpDelta) }}
                <span style="font-weight:400; color:light-dark(#9ca3af,#6b7280);">ΔPY</span>
            </div>
            <div class="bpc-tile-context">
                Reinsurers with no premium in <b>{{ $this->selectedYear }}</b> · Prior year: <b>{{ number_format($withoutOpPl) }}</b>
            </div>
        </div>

    </div>

    {{-- Legend row --}}
    <div style="
        display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;
        padding: 0.45rem 0.25rem; margin-bottom: 1rem;
        font-size: 0.82rem; color: light-dark(#9ca3af, #6b7280);
    ">
        <span style="font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Legend:</span>
        <span><b style="color:light-dark(#374151,#d1d5db);">AC</b> — Actual / Current year ({{ $this->selectedYear }})</span>
        <span><b style="color:light-dark(#374151,#d1d5db);">PY</b> — Prior / Last year ({{ $prevYear }})</span>
        @if($showPlan)
        <span><b style="color:light-dark(#374151,#d1d5db);">PL</b> — Plan / Budget target for {{ $this->selectedYear }}</span>
        <span><b style="color:light-dark(#374151,#d1d5db);">ΔPL</b> — Variance vs budget (AC − Plan)</span>
        @endif
        <span><b style="color:light-dark(#374151,#d1d5db);">ΔPY</b> — Variance vs prior year (AC − PY)</span>
        <span style="display:inline-flex;align-items:center;gap:0.25rem;"><span style="color:#65a30d;font-weight:700;">▲</span> Favorable</span>
        <span style="display:inline-flex;align-items:center;gap:0.25rem;"><span style="color:#dc2626;font-weight:700;">▼</span> Unfavorable</span>
    </div>

    {{-- ── Main card ── --}}
    <div style="
        background: light-dark(#ffffff, #1e2533);
        border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        border-radius: 12px; padding: 1.5rem; font-size: 0.875rem;
    ">
        {{-- Card header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">
                    Gross Premium by Reinsurer
                </h3>
                <p style="font-size:0.875rem; color:light-dark(#6b7280,#9ca3af); margin:0;">
                    {{ $this->selectedYear }} (AC) vs {{ $prevYear }} (PY)
                    @if($showPlan && $this->selectedBudgetId)
                    · vs Plan ({{ $budgets[$this->selectedBudgetId] ?? '' }})
                    @endif
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

        @if(empty($rows))
            <div style="text-align:center; padding:3rem; color:light-dark(#9ca3af,#6b7280);">
                No data available for {{ $this->selectedYear }}.
            </div>

        @elseif($this->chartView === 'bars')
        {{-- ── Bar chart view ── --}}
        @php
            $displayRows  = collect($rows)->sortByDesc('ac')->values()->all();
            $withOpRows   = array_filter($displayRows, fn($r) => $r['ac'] > 0);
            $withOpCount  = count($withOpRows);
            $avgAc        = $withOpCount > 0 ? $totalAc / $withOpCount : 0;
            $avgPct       = $maxAc > 0 ? min(round($avgAc / $maxAc * 100, 1), 100) : 0;
            // Grid: Reinsurer | AC bar | gap | ΔPY | [ΔPL] | ΔPY% | [ΔPL%]
            $gridCols = $showPlan
                ? '18% 5.5rem 1fr 4rem 14% 14% 8% 8%'
                : '20% 5.5rem 1fr 6rem 16% 10%';
        @endphp

        <div>
            {{-- Column headers --}}
            <div style="display:grid; grid-template-columns:{{ $gridCols }}; gap:8px 0; padding:6px 8px;
                        border-bottom:2px solid light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));
                        font-size:0.875rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">
                <div>Reinsurer</div>
                <div wire:click="setSortColumn('ac')" style="cursor:pointer; user-select:none; text-align:right; padding-right:8px;
                     color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }};">
                    AC ({{ $this->selectedYear }}) ↓
                </div>
                <div></div>
                <div></div>
                <div style="text-align:center;">ΔPY</div>
                @if($showPlan)<div style="text-align:center;">ΔPL</div>@endif
                <div style="text-align:right;">ΔPY%</div>
                @if($showPlan)<div style="text-align:right;">ΔPL%</div>@endif
            </div>

            {{-- Data rows --}}
            @foreach ($displayRows as $row)
            @php
                $acPct     = $maxAc > 0 ? min(round($row['ac'] / $maxAc * 100, 1), 100) : 0;
                $isUp      = $row['delta'] >= 0;
                $dColor    = $isUp ? '#65a30d' : '#dc2626';
                $barInner  = $acPct > 25;
                $isPlanUp  = $row['delta_plan'] >= 0;
                $pColor    = $isPlanUp ? '#65a30d' : '#dc2626';
                $dHalfPct  = min($row['bar_pct'] / 2, 50);
                $dInside   = $dHalfPct >= 35;
                $dBarLabel = ($isUp ? '+' : '') . $fmt($row['delta']);
                $pHalfPct  = $showPlan ? min($row['bar_pct_plan'] / 2, 50) : 0;
                $pInside   = $pHalfPct >= 35;
                $pBarLabel = ($isPlanUp ? '+' : '') . $fmt($row['delta_plan']);
            @endphp
            <div style="display:grid; grid-template-columns:{{ $gridCols }}; gap:8px 0; padding:4px 8px; align-items:center;
                        border-bottom:1px solid light-dark(rgba(0,0,0,0.05),rgba(255,255,255,0.05));">

                <div style="font-size:0.875rem; color:light-dark(#111827,#f3f4f6); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $row['name'] }}
                </div>

                {{-- AC value label --}}
                <div style="text-align:right; padding-right:8px; font-size:0.875rem; font-weight:600;
                             color:light-dark(#111827,#f3f4f6); font-variant-numeric:tabular-nums;
                             display:flex; align-items:center; justify-content:flex-end; white-space:nowrap;">
                    {{ $fmt($row['ac']) }}
                </div>

                {{-- AC horizontal bar --}}
                <div style="flex:1; height:22px; position:relative; border-radius:3px;">
                    <div style="position:absolute; inset:0; background:light-dark(#e5e7eb,rgba(255,255,255,0.06)); border-radius:3px;"></div>
                    <div style="position:absolute; top:0; left:0; bottom:0; width:{{ $acPct }}%; background:#41A2C3; border-radius:3px 0 0 3px; z-index:1;"></div>
                    {{-- Plan marker I-beam --}}
                    @if($showPlan && $row['plan'] > 0)
                    @php $planPct = $maxAc > 0 ? min(round($row['plan'] / $maxAc * 100, 1), 100) : 0; @endphp
                    <div style="position:absolute; left:calc({{ $planPct }}% - 1px); top:0; bottom:0; z-index:4; pointer-events:none;">
                        {{-- Top cap --}}
                        <div style="position:absolute; top:0; left:-3px; width:8px; height:2px; background:light-dark(#374151,#ffffff); border-radius:1px;"></div>
                        {{-- Vertical stem --}}
                        <div style="position:absolute; top:0; bottom:0; left:0; width:2px; background:light-dark(#374151,#ffffff);"></div>
                        {{-- Bottom cap --}}
                        <div style="position:absolute; bottom:0; left:-3px; width:8px; height:2px; background:light-dark(#374151,#ffffff); border-radius:1px;"></div>
                        {{-- Label --}}
                        <span style="position:absolute; top:50%; left:7px; transform:translateY(-50%); white-space:nowrap; font-size:0.75rem; font-weight:700; color:light-dark(#374151,#ffffff); line-height:1;">{{ $fmt($row['plan']) }}</span>
                    </div>
                    @endif
                    {{-- Average dashed line --}}
                    <div style="position:absolute; left:{{ $avgPct }}%; top:-3px; bottom:-3px; border-left:2px dashed #C1121F; z-index:3; pointer-events:none;"></div>
                </div>

                <div></div>

                {{-- ΔPY bullet bar --}}
                <div style="position:relative; height:22px;">
                    <div style="position:absolute; left:50%; top:-5px; bottom:-5px; width:1px; background:light-dark(rgba(0,0,0,0.25),rgba(255,255,255,0.25));"></div>
                    @if($isUp)
                    <div style="position:absolute; left:50%; top:4px; bottom:4px; width:{{ $dHalfPct }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                        @if($dInside)<div style="position:absolute; right:4px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dBarLabel }}</div>@endif
                    </div>
                    @if(!$dInside)<div style="position:absolute; left:{{ 50 + $dHalfPct }}%; top:50%; transform:translateY(-50%); padding-left:3px; font-size:0.83rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $dBarLabel }}</div>@endif
                    @else
                    <div style="position:absolute; right:50%; top:4px; bottom:4px; width:{{ $dHalfPct }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                        @if($dInside)<div style="position:absolute; left:4px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dBarLabel }}</div>@endif
                    </div>
                    @if(!$dInside)<div style="position:absolute; right:{{ 50 + $dHalfPct }}%; top:50%; transform:translateY(-50%); padding-right:3px; font-size:0.83rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $dBarLabel }}</div>@endif
                    @endif
                </div>

                {{-- ΔPL bullet bar --}}
                @if($showPlan)
                <div style="position:relative; height:22px;">
                    <div style="position:absolute; left:50%; top:-5px; bottom:-5px; width:1px; background:light-dark(rgba(0,0,0,0.25),rgba(255,255,255,0.25));"></div>
                    @if($row['plan'] > 0 || $row['ac'] > 0)
                        @if($isPlanUp)
                        <div style="position:absolute; left:50%; top:4px; bottom:4px; width:{{ $pHalfPct }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                            @if($pInside)<div style="position:absolute; right:4px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $pBarLabel }}</div>@endif
                        </div>
                        @if(!$pInside)<div style="position:absolute; left:{{ 50 + $pHalfPct }}%; top:50%; transform:translateY(-50%); padding-left:3px; font-size:0.83rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $pBarLabel }}</div>@endif
                        @else
                        <div style="position:absolute; right:50%; top:4px; bottom:4px; width:{{ $pHalfPct }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                            @if($pInside)<div style="position:absolute; left:4px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $pBarLabel }}</div>@endif
                        </div>
                        @if(!$pInside)<div style="position:absolute; right:{{ 50 + $pHalfPct }}%; top:50%; transform:translateY(-50%); padding-right:3px; font-size:0.83rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $pBarLabel }}</div>@endif
                        @endif
                    @endif
                </div>
                @endif

                {{-- ΔPY% --}}
                <div style="text-align:right; font-size:0.83rem; font-weight:600; color:{{ $dColor }};">
                    {{ $isUp ? '+' : '' }}{{ $row['delta_pct'] }}%
                </div>

                {{-- ΔPL% --}}
                @if($showPlan)
                <div style="text-align:right; font-size:0.83rem; font-weight:600; color:{{ $row['plan'] > 0 || $row['ac'] > 0 ? $pColor : 'light-dark(#9ca3af,#6b7280)' }};">
                    @if($row['plan'] > 0 || $row['ac'] > 0)
                        {{ $isPlanUp ? '+' : '' }}{{ $row['delta_pct_plan'] }}%
                    @else
                        —
                    @endif
                </div>
                @endif

            </div>
            @endforeach

            {{-- Avg label --}}
            <div style="display:grid; grid-template-columns:{{ $gridCols }}; gap:8px 0; padding:2px 8px 0;">
                <div></div>
                <div></div>
                <div style="position:relative; height:22px;">
                    <div style="position:absolute; left:{{ $avgPct }}%; transform:translateX(-50%); white-space:nowrap; padding-top:2px;">
                        <span style="display:inline-block; font-size:0.65rem; font-weight:700; color:#ffffff; background:#ef4444; padding:0.18rem 0.45rem; border-radius:999px; line-height:1;">
                            Avg: {{ $fmt($avgAc) }}
                        </span>
                    </div>
                </div>
                @if($showPlan)
                <div></div><div></div><div></div><div></div><div></div>
                @else
                <div></div><div></div><div></div>
                @endif
            </div>

            {{-- Total row --}}
            <div style="display:grid; grid-template-columns:{{ $gridCols }}; gap:8px 0; padding:4px 8px;
                        border-top:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08));
                        background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                <div style="font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</div>
                <div style="text-align:right; padding-right:8px; font-size:0.82rem; font-weight:700; color:light-dark(#111827,#f3f4f6); font-variant-numeric:tabular-nums;">{{ $fmt($totalAc) }}</div>
                <div></div>
                <div></div>
                <div style="text-align:center; font-size:0.83rem; font-weight:700; color:{{ $totalDeltaPy >= 0 ? '#65a30d' : '#dc2626' }};">
                    {{ $totalDeltaPy >= 0 ? '+' : '' }}{{ $fmt($totalDeltaPy) }}
                </div>
                @if($showPlan)
                <div style="text-align:center; font-size:0.83rem; font-weight:700; color:{{ $totalDeltaPln >= 0 ? '#65a30d' : '#dc2626' }};">
                    {{ $totalDeltaPln >= 0 ? '+' : '' }}{{ $fmt($totalDeltaPln) }}
                </div>
                @endif
                <div></div>
                @if($showPlan)<div></div>@endif
            </div>
        </div>

        @else
        {{-- ── Table view ── --}}
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12));">
                        <th wire:click="setSortColumn('code')"
                            style="text-align:left; padding:4px 8px; font-weight:700; width:7%; cursor:pointer;
                                   color:{{ $this->sortColumn === 'code' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }}; user-select:none;">
                            Code {{ $this->sortColumn === 'code' ? '↑' : '' }}
                        </th>
                        <th style="text-align:left; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:16%;">Reinsurer</th>
                        <th wire:click="setSortColumn('ac')"
                            style="text-align:right; padding:4px 8px; font-weight:700; width:10%; cursor:pointer;
                                   color:{{ $this->sortColumn === 'ac' ? '#41A2C3' : 'light-dark(#111827,#f3f4f6)' }}; user-select:none;">
                            AC <span style="font-weight:400; font-size:0.85rem;">({{ $this->selectedYear }})</span>
                            {{ $this->sortColumn === 'ac' ? '↓' : '' }}
                        </th>
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:10%;">
                            PY <span style="font-weight:400; font-size:0.85rem;">({{ $prevYear }})</span>
                        </th>
                        <th style="width:18px; padding:0;"></th>
                        @if($showPlan)
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:10%;">
                            PL
                        </th>
                        <th style="width:18px; padding:0;"></th>
                        @endif
                        <th style="width:5.5rem; padding:0; text-align:center; font-weight:700; font-size:0.82rem; color:light-dark(#6b7280,#9ca3af);">ΔPY</th>
                        @if($showPlan)
                        <th style="width:5.5rem; padding:0; text-align:center; font-weight:700; font-size:0.82rem; color:light-dark(#6b7280,#9ca3af);">ΔPL</th>
                        @endif
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:7%;">ΔPY%</th>
                        @if($showPlan)
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:7%;">ΔPL%</th>
                        @endif
                        @if(!$showPlan)
                        <th style="text-align:right; padding:4px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6); width:7%;">% AC</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                    @php
                        $halfPct  = min($row['bar_pct'] / 2, 50);
                        $dIsPos   = $row['delta'] >= 0;
                        $dColor   = $dIsPos ? '#65a30d' : '#dc2626';
                        $dLabel   = ($dIsPos ? '+' : '') . $fmt($row['delta']);
                        $inside   = $halfPct >= 35;
                        $pHalf    = $showPlan ? min($row['bar_pct_plan'] / 2, 50) : 0;
                        $pIsPos   = $row['delta_plan'] >= 0;
                        $pColor   = $pIsPos ? '#65a30d' : '#dc2626';
                        $pLabel   = ($pIsPos ? '+' : '') . $fmt($row['delta_plan']);
                        $pInside  = $pHalf >= 35;
                    @endphp
                    <tr style="border-bottom:1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));">
                        <td style="padding:4px 8px; color:light-dark(#6b7280,#9ca3af); font-size:0.8rem; font-variant-numeric:tabular-nums;">
                            {{ $row['cns_code'] }}
                        </td>
                        <td style="padding:4px 8px; color:light-dark(#111827,#f3f4f6);">{{ $row['name'] }}</td>
                        <td style="padding:4px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">
                            {{ $fmt($row['ac']) }}
                        </td>
                        <td style="padding:4px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $fmt($row['pl']) }}
                        </td>
                        <td style="width:18px; padding:0;"></td>
                        @if($showPlan)
                        <td style="padding:4px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $row['plan'] > 0 ? $fmt($row['plan']) : '—' }}
                        </td>
                        <td style="width:18px; padding:0;"></td>
                        @endif

                        {{-- ΔPY bullet bar --}}
                        <td style="width:5.5rem; padding:0;">
                            <div style="position:relative; height:20px;">
                                <div style="position:absolute; left:50%; top:-4px; bottom:-4px; width:1px; background:light-dark(rgba(0,0,0,0.25),rgba(255,255,255,0.25));"></div>
                                @if($dIsPos)
                                <div style="position:absolute; left:50%; top:3px; bottom:3px; width:{{ $halfPct }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                                    @if($inside)<div style="position:absolute; right:5px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dLabel }}</div>@endif
                                </div>
                                @if(!$inside)<div style="position:absolute; left:{{ 50 + $halfPct }}%; top:50%; transform:translateY(-50%); padding-left:4px; font-size:0.83rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $dLabel }}</div>@endif
                                @else
                                <div style="position:absolute; right:50%; top:3px; bottom:3px; width:{{ $halfPct }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                                    @if($inside)<div style="position:absolute; left:5px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $dLabel }}</div>@endif
                                </div>
                                @if(!$inside)<div style="position:absolute; right:{{ 50 + $halfPct }}%; top:50%; transform:translateY(-50%); padding-right:4px; font-size:0.83rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $dLabel }}</div>@endif
                                @endif
                            </div>
                        </td>

                        {{-- ΔPL bullet bar --}}
                        @if($showPlan)
                        <td style="width:5.5rem; padding:0;">
                            <div style="position:relative; height:20px;">
                                <div style="position:absolute; left:50%; top:-4px; bottom:-4px; width:1px; background:light-dark(rgba(0,0,0,0.25),rgba(255,255,255,0.25));"></div>
                                @if($row['plan'] > 0 || $row['ac'] > 0)
                                @if($pIsPos)
                                <div style="position:absolute; left:50%; top:3px; bottom:3px; width:{{ $pHalf }}%; background:#65a30d; border-radius:0 2px 2px 0;">
                                    @if($pInside)<div style="position:absolute; right:5px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $pLabel }}</div>@endif
                                </div>
                                @if(!$pInside)<div style="position:absolute; left:{{ 50 + $pHalf }}%; top:50%; transform:translateY(-50%); padding-left:4px; font-size:0.83rem; font-weight:600; color:#65a30d; white-space:nowrap;">{{ $pLabel }}</div>@endif
                                @else
                                <div style="position:absolute; right:50%; top:3px; bottom:3px; width:{{ $pHalf }}%; background:#dc2626; border-radius:2px 0 0 2px;">
                                    @if($pInside)<div style="position:absolute; left:5px; top:50%; transform:translateY(-50%); font-size:0.80rem; font-weight:700; color:#fff; white-space:nowrap;">{{ $pLabel }}</div>@endif
                                </div>
                                @if(!$pInside)<div style="position:absolute; right:{{ 50 + $pHalf }}%; top:50%; transform:translateY(-50%); padding-right:4px; font-size:0.83rem; font-weight:600; color:#dc2626; white-space:nowrap;">{{ $pLabel }}</div>@endif
                                @endif
                                @endif
                            </div>
                        </td>
                        @endif

                        <td style="padding:4px 8px; text-align:right; font-size:0.83rem; font-variant-numeric:tabular-nums; color:{{ $dColor }};">
                            {{ $dIsPos ? '+' : '' }}{{ $row['delta_pct'] }}%
                        </td>
                        @if($showPlan)
                        <td style="padding:4px 8px; text-align:right; font-size:0.83rem; font-variant-numeric:tabular-nums;
                                   color:{{ $row['plan'] > 0 || $row['ac'] > 0 ? $pColor : 'light-dark(#9ca3af,#6b7280)' }};">
                            @if($row['plan'] > 0 || $row['ac'] > 0)
                                {{ $pIsPos ? '+' : '' }}{{ $row['delta_pct_plan'] }}%
                            @else
                                —
                            @endif
                        </td>
                        @endif
                        @if(!$showPlan)
                        <td style="padding:4px 8px; text-align:right; font-size:0.83rem; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                            {{ $totalAc > 0 ? number_format($row['ac'] / $totalAc * 100, 1) . '%' : '—' }}
                        </td>
                        @endif
                    </tr>
                    @endforeach

                    {{-- Totals row --}}
                    <tr style="border-top:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12)); background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                        <td style="padding:5px 8px;"></td>
                        <td style="padding:5px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6);">Total</td>
                        <td style="padding:5px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">{{ $fmt($totalAc) }}</td>
                        <td style="padding:5px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">{{ $fmt($totalPl) }}</td>
                        <td style="width:18px; padding:0;"></td>
                        @if($showPlan)
                        <td style="padding:5px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">{{ $fmt($totalPlan) }}</td>
                        <td style="width:18px; padding:0;"></td>
                        @endif
                        <td style="padding:5px 8px; text-align:center; font-size:0.83rem; font-weight:700; font-variant-numeric:tabular-nums; color:{{ $totalDeltaPy >= 0 ? '#65a30d' : '#dc2626' }};">
                            {{ $totalDeltaPy >= 0 ? '+' : '' }}{{ $fmt($totalDeltaPy) }}
                        </td>
                        @if($showPlan)
                        <td style="padding:5px 8px; text-align:center; font-size:0.83rem; font-weight:700; font-variant-numeric:tabular-nums; color:{{ $totalDeltaPln >= 0 ? '#65a30d' : '#dc2626' }};">
                            {{ $totalDeltaPln >= 0 ? '+' : '' }}{{ $fmt($totalDeltaPln) }}
                        </td>
                        @endif
                        <td style="padding:5px 8px; text-align:right; font-size:0.83rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">
                            {{ $totalPl > 0 ? (($totalDeltaPy >= 0 ? '+' : '') . round($totalDeltaPy / $totalPl * 100, 1) . '%') : '—' }}
                        </td>
                        @if($showPlan)
                        <td style="padding:5px 8px; text-align:right; font-size:0.83rem; font-weight:700; color:{{ $totalDeltaPln >= 0 ? '#65a30d' : '#dc2626' }};">
                            {{ $totalPlan > 0 ? (($totalDeltaPln >= 0 ? '+' : '') . round($totalDeltaPln / $totalPlan * 100, 1) . '%') : '—' }}
                        </td>
                        @endif
                        @if(!$showPlan)
                        <td style="padding:5px 8px; text-align:right; font-size:0.83rem; font-weight:700; color:light-dark(#6b7280,#9ca3af);">100%</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <div style="margin-top:1rem; text-align:right; font-size:0.82rem; color:light-dark(#9ca3af,#6b7280);">
            All figures expressed in millions of US dollars
        </div>
    </div>

</x-filament::section>

{{-- ── Monthly Performance ── --}}
{{-- Both datasets always computed server-side; Alpine x-show toggles visibility client-side --}}
@php
    $bizRows  = $this->getBusinessChartData();
    $bizAcArr = array_column($bizRows, 'ac');
    $bizMaxAc = !empty($bizAcArr) && max($bizAcArr) > 0 ? max($bizAcArr) : 1;
    $bizMaxH  = 160;
    $bizFmt   = fn(int $n): string => $n>=1_000_000 ? number_format($n/1_000_000,1).'M' : ($n>=1_000 ? number_format($n/1_000,1).'K' : (string)$n);
    $bizAvg   = !empty($bizRows) ? array_sum($bizAcArr)/count($bizRows) : 0;
    $bizAvgH  = (int) round(($bizAvg/$bizMaxAc)*$bizMaxH);

    $pyRows   = $this->getPremiumChartDataPY();
    $pyAcArr  = array_column($pyRows, 'ac');
    $pyMaxAc  = !empty($pyAcArr) && max($pyAcArr) > 0 ? (float)max($pyAcArr) : 1.0;
    $pyMaxH   = 160;
    $pyFmt    = fn(float $n): string => abs($n)>=1_000_000 ? number_format($n/1_000_000,1).'M' : (abs($n)>=1_000 ? number_format($n/1_000,1).'K' : number_format($n,0));
    $pyAvg    = !empty($pyRows) ? array_sum($pyAcArr)/count($pyRows) : 0;
    $pyAvgH   = (int) round(($pyAvg/$pyMaxAc)*$pyMaxH);

    $plRows   = $this->getPremiumChartDataPL();
    $plAcArr  = array_column($plRows, 'ac');
    $plMaxAc  = !empty($plAcArr) && max($plAcArr) > 0 ? (float)max($plAcArr) : 1.0;
    $plMaxH   = 160;
    $plFmt    = fn(float $n): string => abs($n)>=1_000_000 ? number_format($n/1_000_000,1).'M' : (abs($n)>=1_000 ? number_format($n/1_000,1).'K' : number_format($n,0));
    $plAvg    = !empty($plRows) ? array_sum($plAcArr)/count($plRows) : 0;
    $plAvgH   = (int) round(($plAvg/$plMaxAc)*$plMaxH);
    $budgetLabel = $this->selectedBudgetId && isset($budgets[$this->selectedBudgetId])
        ? $budgets[$this->selectedBudgetId] : 'Plan';
@endphp

<div style="margin-top: 1.5rem;">
<x-filament::section heading="Monthly Performance">
    <div x-data>

        {{-- PY view: two charts side by side (visible when showPlan=false) --}}
        <div x-show="!$wire.showPlan">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

            {{-- Businesses --}}
            <div style="background:light-dark(#ffffff,#1e2533); border:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08)); border-radius:12px; padding:1.25rem 1.5rem 1rem; font-size:0.875rem;">
                <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">Underwritten Businesses</h3>
                <p style="font-size:0.875rem; color:light-dark(#9ca3af,#6b7280); margin:0 0 0.75rem;">AC: {{ $this->selectedYear }} &nbsp;vs&nbsp; PY: {{ $this->selectedYear - 1 }}</p>
                <div style="display:flex; gap:3px;">
                    @foreach($bizRows as $row)
                    @php $bH=$row['ac']>0?max(($row['ac']/$bizMaxAc)*$bizMaxH,4):0; $bUp=$row['delta_pct']>=0; $bCol=$bUp?'#65a30d':'#dc2626'; @endphp
                    <div style="flex:1; min-width:28px; display:flex; flex-direction:column; align-items:center;">
                        <div style="width:100%; height:{{$bizMaxH}}px; margin-top:2.4rem; position:relative; background:repeating-linear-gradient(to top,transparent 0%,transparent calc(25% - 0.5px),light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.05)) calc(25% - 0.5px),light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.05)) 25%);">
                            <div style="position:absolute; bottom:{{(int)$bH+5}}px; left:0; right:0; display:flex; flex-direction:column; align-items:center;">
                                <span style="font-size:0.80rem; font-weight:700; color:{{$bCol}}; line-height:1.2;">{{ $bUp?'▲':'▼' }}</span>
                                <span style="font-size:0.80rem; font-weight:700; color:{{$bCol}}; line-height:1.2; white-space:nowrap;">{{ ($bUp?'+':'').$row['delta_pct'] }}%</span>
                            </div>
                            <div style="position:absolute; bottom:0; left:0; right:0; height:{{(int)$bH}}px; background:light-dark(#d1d5db,#2d3a4f); border-radius:3px 3px 0 0; overflow:hidden;">
                                @if($row['ac']>0)<div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{$bCol}}; border-radius:3px 3px 0 0;"></div>@endif
                            </div>
                            @if($bizAvg>0)
                            <div style="position:absolute; bottom:{{$bizAvgH}}px; left:0; right:0; border-top:1.5px dashed #ef4444; z-index:2;">
                                @if($loop->last)<span style="position:absolute; right:0; top:-10px; font-size:0.65rem; font-weight:700; color:#fff; background:#ef4444; white-space:nowrap; line-height:1; padding:0.18rem 0.45rem; border-radius:999px;">Avg: {{ $bizFmt((int)round($bizAvg)) }}</span>@endif
                            </div>
                            @endif
                        </div>
                        <div style="font-size:0.85rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); text-align:center; margin-top:5px; white-space:nowrap;">{{ $bizFmt($row['ac']) }}</div>
                        <div style="font-size:0.85rem; color:light-dark(#9ca3af,#6b7280); text-align:center; margin-top:2px;">{{ $row['month'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Premium AC vs PY --}}
            <div style="background:light-dark(#ffffff,#1e2533); border:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08)); border-radius:12px; padding:1.25rem 1.5rem 1rem; font-size:0.875rem;">
                <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">Underwritten Premium</h3>
                <p style="font-size:0.875rem; color:light-dark(#9ca3af,#6b7280); margin:0 0 0.75rem;">AC: {{ $this->selectedYear }} &nbsp;vs&nbsp; PY: {{ $this->selectedYear - 1 }}</p>
                <div style="display:flex; gap:3px;">
                    @foreach($pyRows as $row)
                    @php $pH=$row['ac']>0?max(($row['ac']/$pyMaxAc)*$pyMaxH,4):0; $pUp=$row['delta_pct']>=0; $pCol=$pUp?'#65a30d':'#dc2626'; @endphp
                    <div style="flex:1; min-width:28px; display:flex; flex-direction:column; align-items:center;">
                        <div style="width:100%; height:{{$pyMaxH}}px; margin-top:2.4rem; position:relative; background:repeating-linear-gradient(to top,transparent 0%,transparent calc(25% - 0.5px),light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.05)) calc(25% - 0.5px),light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.05)) 25%);">
                            <div style="position:absolute; bottom:{{(int)$pH+5}}px; left:0; right:0; display:flex; flex-direction:column; align-items:center;">
                                <span style="font-size:0.80rem; font-weight:700; color:{{$pCol}}; line-height:1.2;">{{ $pUp?'▲':'▼' }}</span>
                                <span style="font-size:0.80rem; font-weight:700; color:{{$pCol}}; line-height:1.2; white-space:nowrap;">{{ ($pUp?'+':'').$row['delta_pct'] }}%</span>
                            </div>
                            <div style="position:absolute; bottom:0; left:0; right:0; height:{{(int)$pH}}px; background:light-dark(#d1d5db,#2d3a4f); border-radius:3px 3px 0 0; overflow:hidden;">
                                @if($row['ac']>0)<div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{$pCol}}; border-radius:3px 3px 0 0;"></div>@endif
                            </div>
                            @if($pyAvg>0)
                            <div style="position:absolute; bottom:{{$pyAvgH}}px; left:0; right:0; border-top:1.5px dashed #ef4444; z-index:2;">
                                @if($loop->last)<span style="position:absolute; right:0; top:-10px; font-size:0.65rem; font-weight:700; color:#fff; background:#ef4444; white-space:nowrap; line-height:1; padding:0.18rem 0.45rem; border-radius:999px;">Avg: {{ $pyFmt($pyAvg) }}</span>@endif
                            </div>
                            @endif
                        </div>
                        <div style="font-size:0.85rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); text-align:center; margin-top:5px; white-space:nowrap;">{{ $pyFmt($row['ac']) }}</div>
                        <div style="font-size:0.85rem; color:light-dark(#9ca3af,#6b7280); text-align:center; margin-top:2px;">{{ $row['month'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /grid --}}
        </div>{{-- /x-show PY --}}

        {{-- PL view: single chart full width (visible when showPlan=true) --}}
        <div x-show="$wire.showPlan" style="display:none;">
            <div style="background:light-dark(#ffffff,#1e2533); border:1px solid light-dark(rgba(0,0,0,0.08),rgba(255,255,255,0.08)); border-radius:12px; padding:1.25rem 1.5rem 1rem; font-size:0.875rem;">
                <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">Underwritten Premium</h3>
                <p style="font-size:0.875rem; color:light-dark(#9ca3af,#6b7280); margin:0 0 0.75rem;">
                    AC: {{ $this->selectedYear }} &nbsp;vs&nbsp; <span style="color:#f59e0b; font-weight:600;">PL: {{ $budgetLabel }}</span>
                </p>
                <div style="display:flex; gap:3px;">
                    @foreach($plRows as $row)
                    @php $pH=$row['ac']>0?max(($row['ac']/$plMaxAc)*$plMaxH,4):0; $pUp=$row['delta_pct']>=0; $pCol=$pUp?'#65a30d':'#dc2626'; @endphp
                    <div style="flex:1; min-width:28px; display:flex; flex-direction:column; align-items:center;">
                        <div style="width:100%; height:{{$plMaxH}}px; margin-top:2.4rem; position:relative; background:repeating-linear-gradient(to top,transparent 0%,transparent calc(25% - 0.5px),light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.05)) calc(25% - 0.5px),light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.05)) 25%);">
                            <div style="position:absolute; bottom:{{(int)$pH+5}}px; left:0; right:0; display:flex; flex-direction:column; align-items:center;">
                                <span style="font-size:0.80rem; font-weight:700; color:{{$pCol}}; line-height:1.2;">{{ $pUp?'▲':'▼' }}</span>
                                <span style="font-size:0.80rem; font-weight:700; color:{{$pCol}}; line-height:1.2; white-space:nowrap;">{{ ($pUp?'+':'').$row['delta_pct'] }}%</span>
                            </div>
                            <div style="position:absolute; bottom:0; left:0; right:0; height:{{(int)$pH}}px; background:light-dark(#d1d5db,#2d3a4f); border-radius:3px 3px 0 0; overflow:hidden;">
                                @if($row['ac']>0)<div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{$pCol}}; border-radius:3px 3px 0 0;"></div>@endif
                            </div>
                            @if($plAvg>0)
                            <div style="position:absolute; bottom:{{$plAvgH}}px; left:0; right:0; border-top:1.5px dashed #ef4444; z-index:2;">
                                @if($loop->last)<span style="position:absolute; right:0; top:-10px; font-size:0.65rem; font-weight:700; color:#fff; background:#ef4444; white-space:nowrap; line-height:1; padding:0.18rem 0.45rem; border-radius:999px;">Avg: {{ $plFmt($plAvg) }}</span>@endif
                            </div>
                            @endif
                        </div>
                        <div style="font-size:0.85rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); text-align:center; margin-top:5px; white-space:nowrap;">{{ $plFmt($row['ac']) }}</div>
                        <div style="font-size:0.85rem; color:light-dark(#9ca3af,#6b7280); text-align:center; margin-top:2px;">{{ $row['month'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-filament::section>
</div>{{-- /margin wrapper --}}
</div>

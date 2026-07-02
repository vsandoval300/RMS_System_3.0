@php
    $years = $this->getAvailableYears();
    $rows  = $this->getData();
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
@endphp

<div style="
    background: light-dark(#ffffff, #1e2533);
    border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
    border-radius: 12px;
    padding: 1.5rem;
    font-size: 0.875rem;
">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
        <div>
            <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px;">
                Gross Premium by Reinsurer
            </h3>
            <p style="font-size:0.78rem; color:light-dark(#6b7280,#9ca3af); margin:0;">
                Comparing {{ $this->selectedYear }} (AC) vs {{ $prevYear }} (PL)
            </p>
        </div>

        {{-- Year selector --}}
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <label style="font-size:0.8rem; font-weight:500; color:light-dark(#374151,#d1d5db);">Year:</label>
            <select
                wire:model.live="selectedYear"
                style="
                    background: light-dark(#f9fafb, #2d3748);
                    border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                    border-radius: 6px;
                    padding: 5px 10px;
                    font-size: 0.85rem;
                    color: light-dark(#111827, #f3f4f6);
                    cursor: pointer;
                "
            >
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected($y === $this->selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (empty($rows))
        <div style="text-align:center; padding:3rem; color:light-dark(#9ca3af,#6b7280);">
            No data available for {{ $this->selectedYear }}.
        </div>
    @else
    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12));">
                    <th style="text-align:left; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:8%;">
                        Code
                    </th>
                    <th style="text-align:left; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:18%;">
                        Reinsurer
                    </th>
                    <th style="text-align:right; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:12%;">
                        AC <span style="font-weight:400; font-size:0.85rem;">({{ $this->selectedYear }})</span>
                    </th>
                    <th style="text-align:right; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db); width:12%;">
                        PL <span style="font-weight:400; font-size:0.85rem;">({{ $prevYear }})</span>
                    </th>
                    <th style="text-align:center; padding:6px 8px; font-weight:600; color:light-dark(#374151,#d1d5db);">
                        ΔPL
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                <tr style="border-bottom:1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06)); transition:background .1s;">
                    {{-- CNS Code --}}
                    <td style="padding:7px 8px; color:light-dark(#6b7280,#9ca3af); font-size:0.8rem; font-variant-numeric:tabular-nums;">
                        {{ $row['cns_code'] }}
                    </td>
                    {{-- Reinsurer name --}}
                    <td style="padding:7px 8px; color:light-dark(#111827,#f3f4f6);">
                        {{ $row['name'] }}
                    </td>

                    {{-- AC --}}
                    <td style="padding:7px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">
                        {{ $fmt($row['ac']) }}
                    </td>

                    {{-- PL --}}
                    <td style="padding:7px 8px; text-align:right; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                        {{ $fmt($row['pl']) }}
                    </td>

                    {{-- ΔPL bar --}}
                    <td style="padding:7px 8px;">
                        <div style="display:flex; align-items:center; gap:6px; position:relative;">

                            {{-- Negative side (left) --}}
                            <div style="flex:1; display:flex; justify-content:flex-end; height:18px; position:relative;">
                                @if ($row['delta'] < 0)
                                    <div style="
                                        width: {{ min($row['bar_pct'], 100) }}%;
                                        height:100%;
                                        background:#dc2626;
                                        border-radius:2px 0 0 2px;
                                    "></div>
                                @endif
                            </div>

                            {{-- Center line --}}
                            <div style="width:1px; height:26px; background:light-dark(rgba(0,0,0,0.2),rgba(255,255,255,0.2)); flex-shrink:0;"></div>

                            {{-- Positive side (right) --}}
                            <div style="flex:1; display:flex; justify-content:flex-start; height:18px; position:relative;">
                                @if ($row['delta'] >= 0)
                                    <div style="
                                        width: {{ min($row['bar_pct'], 100) }}%;
                                        height:100%;
                                        background:#65a30d;
                                        border-radius:0 2px 2px 0;
                                    "></div>
                                @endif
                            </div>

                            {{-- Delta label --}}
                            <div style="
                                min-width:72px;
                                text-align:right;
                                font-size:0.8rem;
                                font-weight:500;
                                font-variant-numeric:tabular-nums;
                                color: {{ $row['delta'] >= 0 ? '#65a30d' : '#dc2626' }};
                            ">
                                {{ $row['delta'] >= 0 ? '+' : '' }}{{ $fmt($row['delta']) }}
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach

                {{-- Totals row --}}
                <tr style="border-top:2px solid light-dark(rgba(0,0,0,0.12), rgba(255,255,255,0.12)); background:light-dark(rgba(0,0,0,0.02),rgba(255,255,255,0.03));">
                    <td style="padding:8px 8px;"></td>
                    <td style="padding:8px 8px; font-weight:700; color:light-dark(#111827,#f3f4f6);">
                        Total
                    </td>
                    <td style="padding:8px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#111827,#f3f4f6);">
                        {{ $fmt($totalAc) }}
                    </td>
                    <td style="padding:8px 8px; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:light-dark(#6b7280,#9ca3af);">
                        {{ $fmt($totalPl) }}
                    </td>
                    <td style="padding:8px 8px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="flex:1;"></div>
                            <div style="width:1px; height:20px; background:light-dark(rgba(0,0,0,0.2),rgba(255,255,255,0.2)); flex-shrink:0;"></div>
                            <div style="flex:1;"></div>
                            <div style="
                                min-width:72px;
                                text-align:right;
                                font-size:0.85rem;
                                font-weight:700;
                                font-variant-numeric:tabular-nums;
                                color: {{ $totalDelta >= 0 ? '#65a30d' : '#dc2626' }};
                            ">
                                {{ $totalDelta >= 0 ? '+' : '' }}{{ $fmt($totalDelta) }}
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</div>

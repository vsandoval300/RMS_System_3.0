@php
    $rows  = $this->getChartData();
    $maxAc = max(array_column($rows, 'ac')) ?: 1;
    $maxH  = 160;
    $fmt   = fn(float $n): string => abs($n) >= 1_000_000
        ? number_format($n / 1_000_000, 1) . 'M'
        : (abs($n) >= 1_000 ? number_format($n / 1_000, 1) . 'K' : number_format($n, 0));
@endphp

<div style="
    background: light-dark(#ffffff, #1e2533);
    border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
    border-radius: 12px;
    padding: 1.25rem 1.5rem 1rem;
    font-size: 0.875rem;
">
    <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">
        Underwritten Premium Profile
    </h3>
    <p style="font-size:0.72rem; color:light-dark(#9ca3af,#6b7280); margin:0 0 0.75rem;">
        AC: {{ $this->year }} &nbsp;vs&nbsp; PL: {{ $this->year - 1 }}
    </p>

    <div style="display:flex; gap:3px;">
        @foreach($rows as $row)
        @php
            $barH  = $row['ac'] > 0 ? max(($row['ac'] / $maxAc) * $maxH, 4) : 0;
            $isUp  = $row['delta_pct'] >= 0;
            $color = $isUp ? '#65a30d' : '#dc2626';
            $arrow = $isUp ? '▲' : '▼';
            $sign  = $isUp ? '+' : '';
        @endphp
        <div style="flex:1; min-width:28px; display:flex; flex-direction:column; align-items:center;">

            {{-- Bar area (fixed height + grid lines + delta label overlaid) --}}
            <div style="
                width: 100%;
                height: {{ $maxH }}px;
                margin-top: 2.4rem;
                position: relative;
                background: repeating-linear-gradient(
                    to top,
                    transparent 0%,
                    transparent calc(25% - 0.5px),
                    light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.05)) calc(25% - 0.5px),
                    light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.05)) 25%
                );
            ">
                {{-- Delta indicator: floats just above bar top --}}
                <div style="
                    position: absolute;
                    bottom: {{ $barH + 5 }}px;
                    left: 0; right: 0;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                ">
                    <span style="font-size:0.65rem; font-weight:700; color:{{ $color }}; line-height:1.2;">{{ $arrow }}</span>
                    <span style="font-size:0.65rem; font-weight:700; color:{{ $color }}; line-height:1.2; white-space:nowrap;">{{ $sign }}{{ $row['delta_pct'] }}%</span>
                </div>

                {{-- Bar --}}
                <div style="
                    position: absolute;
                    bottom: 0; left: 0; right: 0;
                    height: {{ $barH }}px;
                    background: light-dark(#d1d5db, #2d3a4f);
                    border-radius: 3px 3px 0 0;
                    overflow: hidden;
                ">
                    @if($row['ac'] > 0)
                    <div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{ $color }}; border-radius:3px 3px 0 0;"></div>
                    @endif
                </div>
            </div>

            {{-- AC value --}}
            <div style="font-size:0.72rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); text-align:center; margin-top:5px; white-space:nowrap;">
                {{ $fmt($row['ac']) }}
            </div>

            {{-- Month label --}}
            <div style="font-size:0.72rem; color:light-dark(#9ca3af,#6b7280); text-align:center; margin-top:2px;">
                {{ $row['month'] }}
            </div>

        </div>
        @endforeach
    </div>
</div>

@php
    $bizData  = $this->getBusinessData();
    $premData = $this->getPremiumData();

    $maxBiz  = count($bizData)  ? max(array_column($bizData,  'total'))   : 1;
    $maxPrem = count($premData) ? max(array_column($premData, 'premium')) : 1;
    $maxH    = 180;

    $fmtN = fn(int $n): string => $n >= 1_000_000
        ? number_format($n / 1_000_000, 1) . 'M'
        : ($n >= 1_000 ? number_format($n / 1_000, 1) . 'K' : (string) $n);

    $fmtF = fn(float $n): string => abs($n) >= 1_000_000
        ? number_format($n / 1_000_000, 1) . 'M'
        : (abs($n) >= 1_000 ? number_format($n / 1_000, 1) . 'K' : number_format($n, 0));
@endphp

<x-filament::section heading="Portfolio Growth">

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

        <div style="display:flex; align-items:center; gap:0.4rem;">
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Reinsurer:</label>
            <select
                wire:model.live="reinsurer"
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
                @foreach($this->getReinsurers() as $id => $name)
                    <option value="{{ $id }}" @selected($id == $this->reinsurer)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── Chart 1: Businesses per Year ── --}}
    <div style="
        background: light-dark(#ffffff, #1e2533);
        border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        border-radius: 12px;
        padding: 1.25rem 1.5rem 1rem;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
    ">
        <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">
            Businesses per Year
        </h3>
        <p style="font-size:0.875rem; color:light-dark(#9ca3af,#6b7280); margin:0 0 0.75rem;">
            Annual count of underwritten businesses · YoY growth indicator
        </p>

        @if(count($bizData))
        <div style="display:flex; gap:5px;">
            @foreach($bizData as $row)
            @php
                $barH     = $row['total'] > 0 ? max(($row['total'] / $maxBiz) * $maxH, 4) : 0;
                $hasDelta = $row['delta_pct'] !== null;
                $isUp     = $hasDelta && $row['delta_pct'] >= 0;
                $capColor = !$hasDelta ? '#41A2C3' : ($isUp ? '#65a30d' : '#dc2626');
                $arrow    = $isUp ? '▲' : '▼';
                $sign     = $isUp ? '+' : '';
            @endphp
            <div style="flex:1; min-width:24px; display:flex; flex-direction:column; align-items:center;">

                {{-- Bar area + delta overhead --}}
                <div style="
                    width: 100%;
                    height: {{ $maxH }}px;
                    margin-top: 2.6rem;
                    position: relative;
                    background: repeating-linear-gradient(
                        to top,
                        transparent 0%,
                        transparent calc(25% - 0.5px),
                        light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.05)) calc(25% - 0.5px),
                        light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.05)) 25%
                    );
                ">
                    {{-- YoY delta indicator above bar --}}
                    @if($hasDelta)
                    <div style="
                        position: absolute;
                        bottom: {{ $barH + 6 }}px;
                        left: 0; right: 0;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    ">
                        <span style="font-size:0.80rem; font-weight:700; color:{{ $capColor }}; line-height:1.2;">{{ $arrow }}</span>
                        <span style="font-size:0.80rem; font-weight:700; color:{{ $capColor }}; line-height:1.2; white-space:nowrap;">{{ $sign }}{{ $row['delta_pct'] }}%</span>
                    </div>
                    @endif

                    {{-- Bar --}}
                    <div style="
                        position: absolute;
                        bottom: 0; left: 0; right: 0;
                        height: {{ $barH }}px;
                        background: light-dark(#d1d5db, #2d3a4f);
                        border-radius: 3px 3px 0 0;
                        overflow: hidden;
                    ">
                        @if($row['total'] > 0)
                        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{ $capColor }}; border-radius:3px 3px 0 0;"></div>
                        @endif
                    </div>
                </div>

                {{-- Value --}}
                <div style="font-size:0.80rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); text-align:center; margin-top:5px; white-space:nowrap;">
                    {{ $fmtN($row['total']) }}
                </div>

                {{-- Year label --}}
                <div style="font-size:0.90rem; color:light-dark(#9ca3af,#6b7280); text-align:center; margin-top:2px; white-space:nowrap;">
                    {{ $row['year'] }}
                </div>

            </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div style="display:flex; gap:1rem; margin-top:0.75rem; padding-top:0.5rem; border-top:1px solid light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.06));">
            <span style="display:flex; align-items:center; gap:4px; font-size:0.875rem; color:light-dark(#6b7280,#9ca3af);">
                <span style="width:10px; height:3px; background:#41A2C3; border-radius:2px; display:inline-block;"></span> First year
            </span>
            <span style="display:flex; align-items:center; gap:4px; font-size:0.875rem; color:light-dark(#6b7280,#9ca3af);">
                <span style="width:10px; height:3px; background:#65a30d; border-radius:2px; display:inline-block;"></span> YoY Growth
            </span>
            <span style="display:flex; align-items:center; gap:4px; font-size:0.875rem; color:light-dark(#6b7280,#9ca3af);">
                <span style="width:10px; height:3px; background:#dc2626; border-radius:2px; display:inline-block;"></span> YoY Decline
            </span>
        </div>
        @else
        <p style="font-size:0.8rem; color:light-dark(#9ca3af,#6b7280);">No data available.</p>
        @endif
    </div>

    {{-- ── Chart 2: Underwritten Premium ── --}}
    <div style="
        background: light-dark(#ffffff, #1e2533);
        border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
        border-radius: 12px;
        padding: 1.25rem 1.5rem 1rem;
        font-size: 0.875rem;
    ">
        <h3 style="font-size:1.05rem; font-weight:600; margin:0 0 2px; color:light-dark(#111827,#f3f4f6);">
            Underwritten Premium
        </h3>
        <p style="font-size:0.875rem; color:light-dark(#9ca3af,#6b7280); margin:0 0 0.75rem;">
            Annual FTS premium in USD · YoY growth indicator
        </p>

        @if(count($premData))
        <div style="display:flex; gap:5px;">
            @foreach($premData as $row)
            @php
                $barH     = $row['premium'] > 0 ? max(($row['premium'] / $maxPrem) * $maxH, 4) : 0;
                $hasDelta = $row['delta_pct'] !== null;
                $isUp     = $hasDelta && $row['delta_pct'] >= 0;
                $capColor = !$hasDelta ? '#41A2C3' : ($isUp ? '#65a30d' : '#dc2626');
                $arrow    = $isUp ? '▲' : '▼';
                $sign     = $isUp ? '+' : '';
            @endphp
            <div style="flex:1; min-width:24px; display:flex; flex-direction:column; align-items:center;">

                {{-- Bar area + delta overhead --}}
                <div style="
                    width: 100%;
                    height: {{ $maxH }}px;
                    margin-top: 2.6rem;
                    position: relative;
                    background: repeating-linear-gradient(
                        to top,
                        transparent 0%,
                        transparent calc(25% - 0.5px),
                        light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.05)) calc(25% - 0.5px),
                        light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.05)) 25%
                    );
                ">
                    {{-- YoY delta indicator above bar --}}
                    @if($hasDelta)
                    <div style="
                        position: absolute;
                        bottom: {{ $barH + 6 }}px;
                        left: 0; right: 0;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    ">
                        <span style="font-size:0.80rem; font-weight:700; color:{{ $capColor }}; line-height:1.2;">{{ $arrow }}</span>
                        <span style="font-size:0.80rem; font-weight:700; color:{{ $capColor }}; line-height:1.2; white-space:nowrap;">{{ $sign }}{{ $row['delta_pct'] }}%</span>
                    </div>
                    @endif

                    {{-- Bar --}}
                    <div style="
                        position: absolute;
                        bottom: 0; left: 0; right: 0;
                        height: {{ $barH }}px;
                        background: light-dark(#d1d5db, #2d3a4f);
                        border-radius: 3px 3px 0 0;
                        overflow: hidden;
                    ">
                        @if($row['premium'] > 0)
                        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{ $capColor }}; border-radius:3px 3px 0 0;"></div>
                        @endif
                    </div>
                </div>

                {{-- Value --}}
                <div style="font-size:0.80rem; font-weight:500; color:light-dark(#6b7280,#9ca3af); text-align:center; margin-top:5px; white-space:nowrap;">
                    {{ $fmtF($row['premium']) }}
                </div>

                {{-- Year label --}}
                <div style="font-size:0.90rem; color:light-dark(#9ca3af,#6b7280); text-align:center; margin-top:2px; white-space:nowrap;">
                    {{ $row['year'] }}
                </div>

            </div>
            @endforeach
        </div>

        {{-- Footer note --}}
        <div style="margin-top:0.75rem; padding-top:0.5rem; border-top:1px solid light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.06));">
            <span style="font-size:0.875rem; color:light-dark(#9ca3af,#6b7280);">All figures expressed in millions of US dollars</span>
        </div>
        @else
        <p style="font-size:0.8rem; color:light-dark(#9ca3af,#6b7280);">No data available.</p>
        @endif
    </div>

</x-filament::section>

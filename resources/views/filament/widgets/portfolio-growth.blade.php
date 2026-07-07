@php
    $bizData  = $this->getBusinessData();
    $premData = $this->getPremiumData();
    $kpi      = $this->getKpiData();

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

    {{-- ── KPI Tiles ── --}}
    @php
        $kpiCagr   = $kpi['cagr'];
        $kpiCum    = $kpi['cumulative'];
        $kpiRecY   = $kpi['record_year'];
        $kpiRecP   = $kpi['record_premium'];
        $kpiRecB   = $kpi['record_biz'];
        $kpiActNow = $kpi['active_now'];
        $kpiActPrv = $kpi['active_prev'];
        $kpiActYr  = $kpi['active_year'];

        $kpiActDelta = $kpiActPrv > 0 ? $kpiActNow - $kpiActPrv : null;
        $kpiActDSign = ($kpiActDelta !== null && $kpiActDelta >= 0) ? '+' : '';

        $fmtBig = function (float $n): string {
            return abs($n) >= 1_000_000_000
                ? '$' . number_format($n / 1_000_000_000, 2) . 'B'
                : (abs($n) >= 1_000_000 ? '$' . number_format($n / 1_000_000, 1) . 'M'
                : (abs($n) >= 1_000     ? '$' . number_format($n / 1_000, 1) . 'K'
                : '$' . number_format($n, 0)));
        };
    @endphp

    <div style="
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.85rem;
        margin-bottom: 1.25rem;
    " class="pg-kpi-grid">
        <style>
            @media (max-width: 900px) { .pg-kpi-grid { grid-template-columns: repeat(2, 1fr) !important; } }
            @media (max-width: 540px) { .pg-kpi-grid { grid-template-columns: 1fr !important; } }

            .pg-tile {
                background: light-dark(#ffffff, #1e2533);
                border: 1px solid light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
                border-radius: 12px;
                padding: 1rem 1.15rem 0.85rem;
                display: flex;
                flex-direction: column;
                gap: 0;
                position: relative;
                overflow: hidden;
            }
            .pg-tile-label {
                font-size: 0.74rem;
                font-weight: 600;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                color: light-dark(#9ca3af, #6b7280);
                margin-bottom: 0.3rem;
            }
            .pg-tile-value {
                font-size: 1.65rem;
                font-weight: 700;
                line-height: 1.15;
                color: light-dark(#111827, #f3f4f6);
                font-variant-numeric: tabular-nums;
                margin-bottom: 0.2rem;
            }
            .pg-tile-sub {
                font-size: 0.84rem;
                color: light-dark(#6b7280, #9ca3af);
                margin-bottom: 0.65rem;
            }
            .pg-tile-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.2rem;
                font-size: 0.84rem;
                font-weight: 700;
                border-radius: 999px;
                padding: 0.18rem 0.55rem;
                position: absolute;
                top: 0.85rem;
                right: 0.85rem;
            }
            .pg-tile-badge.up {
                background: light-dark(#dcfce7, #14532d);
                color: light-dark(#15803d, #4ade80);
            }
            .pg-tile-badge.down {
                background: light-dark(#fee2e2, #450a0a);
                color: light-dark(#b91c1c, #f87171);
            }
            .pg-tile-badge.neutral {
                background: light-dark(#f3f4f6, #1f2937);
                color: light-dark(#6b7280, #9ca3af);
            }
            .pg-tile-accent {
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                border-radius: 12px 12px 0 0;
            }
            canvas.pg-spark {
                display: block;
                width: 100%;
                height: 32px;
                margin-top: auto;
                opacity: 0.85;
            }
        </style>

        {{-- Tile 1: Premium CAGR --}}
        <div class="pg-tile">
            <div class="pg-tile-accent" style="background: linear-gradient(90deg, #3b82f6, #6366f1);"></div>
            @if($kpiCagr !== null)
                <span class="pg-tile-badge {{ $kpiCagr >= 0 ? 'up' : 'down' }}">
                    {{ $kpiCagr >= 0 ? '▲ +' : '▼ -' }}{{ abs($kpiCagr) }}%
                </span>
            @endif
            <div class="pg-tile-label" style="margin-top:0.35rem;">Premium CAGR</div>
            <div class="pg-tile-value" style="color: {{ $kpiCagr === null ? 'light-dark(#9ca3af,#6b7280)' : ($kpiCagr >= 0 ? 'light-dark(#1d4ed8,#60a5fa)' : 'light-dark(#b91c1c,#f87171)') }};">
                {{ $kpiCagr !== null ? ($kpiCagr >= 0 ? '+' : '') . $kpiCagr . '%' : 'N/A' }}
            </div>
            <div class="pg-tile-sub">Compound annual growth rate</div>
            <canvas class="pg-spark"
                data-spark="{{ json_encode($kpi['prem_sparkline']) }}"
                data-color="#3b82f6"
                data-fill="#3b82f620"></canvas>
        </div>

        {{-- Tile 2: Cumulative Premium --}}
        <div class="pg-tile">
            <div class="pg-tile-accent" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="pg-tile-label" style="margin-top:0.35rem;">Cumulative Premium</div>
            <div class="pg-tile-value">{{ $fmtBig($kpiCum) }}</div>
            <div class="pg-tile-sub">Total USD across all years</div>
            <canvas class="pg-spark"
                data-spark="{{ json_encode($kpi['cum_sparkline']) }}"
                data-color="#10b981"
                data-fill="#10b98120"></canvas>
        </div>

        {{-- Tile 3: Record Year --}}
        <div class="pg-tile">
            <div class="pg-tile-accent" style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
            <span class="pg-tile-badge neutral">Peak</span>
            <div class="pg-tile-label" style="margin-top:0.35rem;">Record Year</div>
            <div class="pg-tile-value" style="color:light-dark(#d97706,#fbbf24);">
                {{ $kpiRecY ?? '—' }}
            </div>
            <div class="pg-tile-sub">
                @if($kpiRecY)
                    {{ $fmtBig($kpiRecP) }} · {{ $kpiRecB }} {{ $kpiRecB === 1 ? 'business' : 'businesses' }}
                @else
                    No data available
                @endif
            </div>
            <canvas class="pg-spark"
                data-spark="{{ json_encode($kpi['prem_sparkline']) }}"
                data-color="#f59e0b"
                data-fill="#f59e0b20"></canvas>
        </div>

        {{-- Tile 4: Active Reinsurers --}}
        <div class="pg-tile">
            <div class="pg-tile-accent" style="background: linear-gradient(90deg, #8b5cf6, #6d28d9);"></div>
            @if($kpiActDelta !== null)
                <span class="pg-tile-badge {{ $kpiActDelta >= 0 ? 'up' : 'down' }}">
                    {{ $kpiActDelta >= 0 ? '▲ +' : '▼ -' }}{{ abs($kpiActDelta) }}
                </span>
            @endif
            <div class="pg-tile-label" style="margin-top:0.35rem;">Active Reinsurers</div>
            <div class="pg-tile-value">{{ $kpiActNow ?: '—' }}</div>
            <div class="pg-tile-sub">
                In {{ $kpiActYr }}
                @if($kpiActDelta !== null)
                    · {{ $kpiActDSign }}{{ $kpiActDelta }} vs {{ $kpiActYr - 1 }}
                @endif
            </div>
            <canvas class="pg-spark"
                data-spark="{{ json_encode($kpi['rein_sparkline']) }}"
                data-color="#8b5cf6"
                data-fill="#8b5cf620"></canvas>
        </div>
    </div>

    <script>
    (function () {
        function drawSparkline(canvas) {
            const raw   = canvas.dataset.spark;
            const color = canvas.dataset.color || '#6366f1';
            const fill  = canvas.dataset.fill  || color + '20';
            if (!raw) return;

            const data = JSON.parse(raw);
            if (!data || data.length < 2) return;

            const dpr = window.devicePixelRatio || 1;
            const W   = canvas.offsetWidth  || 160;
            const H   = canvas.offsetHeight || 32;
            canvas.width  = W * dpr;
            canvas.height = H * dpr;
            const ctx = canvas.getContext('2d');
            ctx.scale(dpr, dpr);

            const min = Math.min(...data);
            const max = Math.max(...data);
            const range = max - min || 1;

            const pad = { t: 3, b: 3, l: 2, r: 2 };
            const iW  = W - pad.l - pad.r;
            const iH  = H - pad.t - pad.b;

            const pts = data.map((v, i) => ({
                x: pad.l + (i / (data.length - 1)) * iW,
                y: pad.t + (1 - (v - min) / range) * iH,
            }));

            // Area fill
            ctx.beginPath();
            ctx.moveTo(pts[0].x, H - pad.b);
            pts.forEach(p => ctx.lineTo(p.x, p.y));
            ctx.lineTo(pts[pts.length - 1].x, H - pad.b);
            ctx.closePath();
            ctx.fillStyle = fill;
            ctx.fill();

            // Line
            ctx.beginPath();
            pts.forEach((p, i) => i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y));
            ctx.strokeStyle = color;
            ctx.lineWidth   = 1.5;
            ctx.lineJoin    = 'round';
            ctx.stroke();

            // Endpoint dot
            const last = pts[pts.length - 1];
            ctx.beginPath();
            ctx.arc(last.x, last.y, 2.5, 0, Math.PI * 2);
            ctx.fillStyle = color;
            ctx.fill();
        }

        function drawAll() {
            document.querySelectorAll('canvas.pg-spark').forEach(drawSparkline);
        }

        document.addEventListener('DOMContentLoaded', drawAll);
        document.addEventListener('livewire:updated',  drawAll);
        // Filament/Livewire v3 dispatches this event
        document.addEventListener('livewire:navigated', drawAll);
        // Run once now in case DOM is already ready
        if (document.readyState !== 'loading') drawAll();
    })();
    </script>

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
            <b style="color:light-dark(#374151,#d1d5db);">CAGR</b> — Compound Annual Growth Rate from first recorded year to prior year
        </span>
        <span>
            <b style="color:light-dark(#374151,#d1d5db);">ΔPL</b> — Change vs prior year
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.25rem;">
            <span style="color:#65a30d;font-weight:700;">▲</span> Favorable
        </span>
        <span style="display:inline-flex;align-items:center;gap:0.25rem;">
            <span style="color:#dc2626;font-weight:700;">▼</span> Unfavorable
        </span>
        <span>
            Sparklines show historical trend per metric
        </span>
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

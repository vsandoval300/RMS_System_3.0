@php
    $seg  = $this->getDendrogramData();
    $n    = $seg['n'];
    $k    = $seg['k'];

    $fmtP = fn(float $v): string => abs($v) >= 1_000_000
        ? '$' . number_format($v / 1_000_000, 1) . 'M'
        : (abs($v) >= 1_000 ? '$' . number_format($v / 1_000, 1) . 'K' : '$' . number_format($v, 0));

    $clusterColors = ['#41A2C3', '#f59e0b', '#8b5cf6'];
    $clusterNames  = ['Cluster A', 'Cluster B', 'Cluster C'];

    // Cluster summary for the legend cards
    $clusterSummaries = [];
    foreach ($seg['leaves'] as $leaf) {
        $ci = $leaf['cluster'];
        if (! isset($clusterSummaries[$ci])) {
            $clusterSummaries[$ci] = ['count' => 0, 'biz' => 0, 'premium' => 0.0];
        }
        $clusterSummaries[$ci]['count']++;
        $clusterSummaries[$ci]['biz']     += $leaf['biz'];
        $clusterSummaries[$ci]['premium'] += $leaf['premium'];
    }
    ksort($clusterSummaries);
@endphp

<x-filament::section heading="Reinsurer Segmentation">

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
        <span style="font-size:0.8rem; font-weight:600; color:light-dark(#6b7280,#9ca3af); letter-spacing:0.03em; text-transform:uppercase;">Filters</span>
        <div style="width:1px; height:22px; background:light-dark(rgba(0,0,0,0.12),rgba(255,255,255,0.12));"></div>
        <div style="display:flex; align-items:center; gap:0.4rem;">
            <label style="font-size:0.95rem; font-weight:500; color:light-dark(#111827,#f3f4f6);">Year:</label>
            <select wire:model.live="selectedYear" style="
                background: light-dark(#ffffff, #1e2533);
                border: 1px solid light-dark(rgba(0,0,0,0.15), rgba(255,255,255,0.15));
                border-radius: 6px; padding: 7px 28px 7px 10px;
                font-size: 0.85rem; color: light-dark(#111827, #f3f4f6);
                cursor: pointer; appearance: auto;
            ">
                @foreach($this->getAvailableYears() as $y)
                    <option value="{{ $y }}" @selected($y == $this->selectedYear)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($n < 2)
        <p style="text-align:center; color:light-dark(#9ca3af,#6b7280); padding:3rem 0; font-size:0.9rem;">
            Not enough data for {{ $this->selectedYear }} to compute segmentation.
        </p>
    @else

    {{-- ── Info bar ── --}}
    <div style="
        display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;
        padding: 0.55rem 0.75rem; margin-bottom: 1rem;
        background: light-dark(#f9fafb, #1a2236);
        border: 1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));
        border-radius: 8px; font-size: 0.78rem; color: light-dark(#6b7280,#9ca3af);
    ">
        <span style="font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Hierarchical Clustering</span>
        <span>Complete-linkage agglomerative clustering · Euclidean distance on standardized variables</span>
        <span>·</span>
        <span><b style="color:light-dark(#374151,#d1d5db);">{{ $n }}</b> reinsurers · <b style="color:light-dark(#374151,#d1d5db);">{{ $k }}</b> clusters · Year <b style="color:light-dark(#374151,#d1d5db);">{{ $this->selectedYear }}</b></span>
        <span>·</span>
        <span>Variables: businesses, premium (USD)</span>
    </div>

    {{-- ── Dendrogram canvas ── --}}
    <div style="position:relative; width:100%;">
        <canvas id="seg-dendrogram"
            data-dendro="{{ json_encode($seg) }}"
            width="900" height="480"
            style="width:100%; height:480px; display:block;"></canvas>
    </div>

    {{-- ── Cluster summary cards ── --}}
    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1.1rem; padding-top:0.9rem; border-top:1px solid light-dark(rgba(0,0,0,0.06),rgba(255,255,255,0.06));">
        @foreach($clusterSummaries as $ci => $cs)
        @php
            $avgBiz  = $cs['count'] ? round($cs['biz'] / $cs['count']) : 0;
            $avgPrem = $cs['count'] ? $cs['premium'] / $cs['count'] : 0;
        @endphp
        <div style="
            flex: 1; min-width: 200px;
            display: flex; align-items: center; gap: 0.75rem;
            background: light-dark(#f9fafb, #1a2236);
            border: 1px solid light-dark(rgba(0,0,0,0.06), rgba(255,255,255,0.06));
            border-left: 3px solid {{ $clusterColors[$ci] ?? '#999' }};
            border-radius: 8px; padding: 0.5rem 0.85rem;
        ">
            <div>
                <div style="font-size:0.70rem; font-weight:600; letter-spacing:0.07em; text-transform:uppercase; color:light-dark(#9ca3af,#6b7280); margin-bottom:0.2rem;">
                    {{ $clusterNames[$ci] ?? 'Cluster ' . ($ci+1) }}
                </div>
                <div style="font-size:0.85rem; color:light-dark(#374151,#d1d5db);">
                    <b>{{ $cs['count'] }}</b> {{ $cs['count'] === 1 ? 'reinsurer' : 'reinsurers' }}
                    &nbsp;·&nbsp; Avg <b>{{ $avgBiz }}</b> biz
                    &nbsp;·&nbsp; Avg <b>{{ $fmtP($avgPrem) }}</b>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Legend ── --}}
    <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap; padding:0.45rem 0.25rem; margin-top:0.75rem; font-size:0.82rem; color:light-dark(#9ca3af,#6b7280);">
        <span style="font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Legend:</span>
        <span>Y-axis: Euclidean distance in standardized space — higher = more dissimilar</span>
        <span style="display:inline-flex; align-items:center; gap:0.3rem;">
            <span style="display:inline-block; width:20px; height:0; border-top:2px dashed #ef4444;"></span>
            Cut line — defines the {{ $k }} cluster groups
        </span>
        <span>Leaf labels colored by cluster assignment</span>
    </div>

    @endif

</x-filament::section>

@script
<script>
(function () {
    const COLORS        = ['#41A2C3', '#f59e0b', '#8b5cf6'];
    const NEUTRAL_DARK  = '#4b5563';
    const NEUTRAL_LIGHT = '#9ca3af';

    function draw() {
        const canvas = document.getElementById('seg-dendrogram');
        if (!canvas) return;

        const raw = canvas.dataset.dendro;
        if (!raw) return;

        let data;
        try { data = JSON.parse(raw); } catch (e) { return; }

        const { leaves, merges, clusterOf, cutHeight, maxHeight, n } = data;
        if (!merges || merges.length === 0 || n < 2 || !maxHeight || maxHeight <= 0) return;

        const isDark = document.documentElement.dataset.theme === 'dark'
            || (!document.documentElement.dataset.theme && window.matchMedia('(prefers-color-scheme: dark)').matches);

        const dpr  = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        const W    = Math.round(rect.width  > 0 ? rect.width  : (canvas.offsetWidth  || 900));
        const H    = Math.round(rect.height > 0 ? rect.height : (canvas.offsetHeight || 480));

        canvas.width  = W * dpr;
        canvas.height = H * dpr;
        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);

        const textColor    = isDark ? '#9ca3af' : '#6b7280';
        const gridColor    = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
        const neutralColor = isDark ? NEUTRAL_DARK : NEUTRAL_LIGHT;
        const bgColor      = isDark ? '#1e2533' : '#ffffff';

        // Fill background
        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, W, H);

        const PAD   = { t: 28, b: 140, l: 62, r: 24 };
        const plotW = W - PAD.l - PAD.r;
        const plotH = H - PAD.t - PAD.b;

        // ── Leaf ordering via DFS ──────────────────────────────
        const rootId   = merges[merges.length - 1].id;
        const mergeMap = {};
        merges.forEach(m => { mergeMap[m.id] = m; });

        function leafOrder(nodeId) {
            if (nodeId < n) return [nodeId];
            const m = mergeMap[nodeId];
            if (!m) return [nodeId]; // safety
            return [...leafOrder(m.left), ...leafOrder(m.right)];
        }
        const ordered = leafOrder(rootId);
        const leafPos = {};
        ordered.forEach((id, i) => { leafPos[id] = i; });

        // ── Node x positions ──────────────────────────────────
        const nodeXcache = {};
        function nodeX(id) {
            if (nodeXcache[id] !== undefined) return nodeXcache[id];
            if (id < n) { nodeXcache[id] = leafPos[id]; return nodeXcache[id]; }
            const m = mergeMap[id];
            if (!m) { nodeXcache[id] = 0; return 0; }
            nodeXcache[id] = (nodeX(m.left) + nodeX(m.right)) / 2;
            return nodeXcache[id];
        }
        for (let i = 0; i < n; i++) nodeX(i);
        merges.forEach(m => nodeX(m.id));

        // ── Node heights ──────────────────────────────────────
        const nodeH = {};
        for (let i = 0; i < n; i++) nodeH[i] = 0;
        merges.forEach(m => { nodeH[m.id] = m.height; });

        // ── Cluster color per node ────────────────────────────
        const nodeCluster = {};
        for (let i = 0; i < n; i++) nodeCluster[i] = clusterOf[i];
        merges.forEach(m => {
            nodeCluster[m.id] = (cutHeight > 0 && m.height < cutHeight) ? nodeCluster[m.left] : -1;
        });

        // ── Canvas helpers ────────────────────────────────────
        const toX = pos => PAD.l + (pos + 0.5) * (plotW / n);
        const toY = h   => PAD.t + (1 - h / maxHeight) * plotH;

        ctx.clearRect(0, 0, W, H);

        // ── Y-axis grid + labels ──────────────────────────────
        const nTicks = 5;
        ctx.font         = `${10.5}px system-ui, sans-serif`;
        ctx.fillStyle    = textColor;
        ctx.textAlign    = 'right';
        ctx.textBaseline = 'middle';
        ctx.strokeStyle  = gridColor;
        ctx.lineWidth    = 1;

        for (let i = 0; i <= nTicks; i++) {
            const hv = (i / nTicks) * maxHeight;
            const cy = toY(hv);
            ctx.beginPath(); ctx.moveTo(PAD.l, cy); ctx.lineTo(W - PAD.r, cy); ctx.stroke();
            ctx.fillText(hv.toFixed(1), PAD.l - 5, cy);
        }

        // Y-axis title
        ctx.save();
        ctx.translate(13, PAD.t + plotH / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'top';
        ctx.font         = `bold ${11}px system-ui, sans-serif`;
        ctx.fillStyle    = textColor;
        ctx.fillText('Distance (Euclidean)', 0, 0);
        ctx.restore();

        // ── Cut line ──────────────────────────────────────────
        if (cutHeight > 0) {
            const cy = toY(cutHeight);
            ctx.save();
            ctx.strokeStyle = '#ef4444';
            ctx.lineWidth   = 1.5;
            ctx.setLineDash([6, 4]);
            ctx.beginPath(); ctx.moveTo(PAD.l, cy); ctx.lineTo(W - PAD.r, cy); ctx.stroke();
            ctx.setLineDash([]);
            ctx.fillStyle    = '#ef4444';
            ctx.font         = `bold ${11}px system-ui, sans-serif`;
            ctx.textAlign    = 'right';
            ctx.textBaseline = 'bottom';
            ctx.fillText('cut', W - PAD.r, cy - 3);
            ctx.restore();
        }

        // ── Dendrogram segments ───────────────────────────────
        ctx.lineWidth = 2;
        ctx.lineCap   = 'square';

        merges.forEach(m => {
            const lx = toX(nodeX(m.left));
            const rx = toX(nodeX(m.right));
            const y  = toY(m.height);
            const ly = toY(nodeH[m.left]);
            const ry = toY(nodeH[m.right]);

            const clr = nodeCluster[m.id] >= 0 ? COLORS[nodeCluster[m.id]] : neutralColor;
            ctx.strokeStyle = clr;

            ctx.beginPath(); ctx.moveTo(lx, ly); ctx.lineTo(lx, y); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(rx, ry); ctx.lineTo(rx, y); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(lx, y);  ctx.lineTo(rx, y); ctx.stroke();
        });

        // ── Leaf dots ─────────────────────────────────────────
        for (let i = 0; i < n; i++) {
            const cx = toX(leafPos[i]);
            const cy = toY(0);
            ctx.beginPath();
            ctx.arc(cx, cy, 3, 0, Math.PI * 2);
            ctx.fillStyle = COLORS[clusterOf[i]];
            ctx.fill();
        }

        // ── Leaf labels (rotated −90°) ────────────────────────
        ctx.font         = `${11.5}px system-ui, sans-serif`;
        ctx.textAlign    = 'right';
        ctx.textBaseline = 'middle';

        ordered.forEach((leafId, i) => {
            const cx = toX(i);
            const cy = toY(0) + 10;
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(-Math.PI / 2);
            ctx.fillStyle = COLORS[clusterOf[leafId]];
            ctx.fillText(leaves[leafId].name, 0, 0);
            ctx.restore();
        });
    }

    // Retry with requestAnimationFrame until canvas has real layout dimensions
    function tryDraw(remaining) {
        const canvas = document.getElementById('seg-dendrogram');
        if (!canvas) {
            if (remaining > 0) requestAnimationFrame(() => tryDraw(remaining - 1));
            return;
        }
        const rect = canvas.getBoundingClientRect();
        if (rect.width > 0) {
            draw();
            return;
        }
        if (remaining > 0) {
            requestAnimationFrame(() => tryDraw(remaining - 1));
        }
    }

    // Start trying — 60 frames ≈ 1 second at 60fps
    tryDraw(60);

    // Redraw when data-dendro changes (Livewire morphs the attribute after year filter update)
    const _segCanvas = document.getElementById('seg-dendrogram');
    if (_segCanvas) {
        new MutationObserver(() => {
            requestAnimationFrame(() => draw());
        }).observe(_segCanvas, { attributes: true, attributeFilter: ['data-dendro'] });
    }

    // Redraw on resize
    window.addEventListener('resize', draw);

    // Redraw on theme change
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', draw);
    new MutationObserver(draw).observe(document.documentElement, {
        attributes: true, attributeFilter: ['data-theme']
    });
})();
</script>
@endscript

<div class="tp-wrap">
<style>
    .tp-wrap {
        --tp-card:       light-dark(#ffffff, #18181B);
        --tp-border:     light-dark(#e5e7eb, #27272a);
        --tp-text:       light-dark(#111827, #f4f4f5);
        --tp-text-sec:   light-dark(#374151, #d4d4d8);
        --tp-text-muted: light-dark(#6b7280, #a1a1aa);
        --tp-row-hover:  light-dark(#f9fafb, #1c1c1f);
        --tp-tbl-head:   light-dark(#f3f4f6, #1D1D20);
        --tp-accent:     #41A2C3;
        --tp-success:    #16a34a;
        --tp-warning:    #d97706;
        --tp-danger:     #dc2626;
    }

    /* ── Layout ── */
    .tp-section    { background:var(--tp-card); border:1px solid var(--tp-border); border-radius:0.75rem; overflow:hidden; margin-bottom:1rem; }
    .tp-sec-header { padding:0.85rem 1.25rem; border-bottom:1px solid var(--tp-border); display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap; }
    .tp-sec-title  { font-size:0.875rem; font-weight:700; color:var(--tp-text); display:flex; align-items:center; gap:0.5rem; }
    .tp-sec-body   { padding:1.25rem; }

    /* ── Period filter ── */
    .tp-filter-bar   { display:flex; align-items:center; gap:0.4rem; margin-bottom:0.75rem; flex-wrap:wrap; }
    .tp-period-btn   { font-size:0.8rem; font-weight:600; padding:0.3rem 0.85rem; border-radius:9999px; border:1px solid var(--tp-border); background:transparent; color:var(--tp-text-muted); cursor:pointer; transition:all .15s; white-space:nowrap; }
    .tp-period-btn.active { background:var(--tp-accent); color:#fff; border-color:var(--tp-accent); }
    .tp-period-btn.quarter { font-size:0.75rem; padding:0.3rem 0.65rem; }
    .tp-period-divider { width:1px; height:18px; background:var(--tp-border); margin:0 0.25rem; flex-shrink:0; }
    .tp-period-label { font-size:0.8rem; color:var(--tp-text-muted); margin-left:auto; white-space:nowrap; }
    /* ── Custom range panel ── */
    .tp-custom-panel { display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; margin-bottom:1rem;
                       background:var(--tp-card); border:1px solid var(--tp-border); border-radius:0.65rem;
                       padding:0.75rem 1rem; }
    .tp-custom-label { font-size:0.78rem; font-weight:600; color:var(--tp-text-muted); white-space:nowrap; }
    .tp-custom-input { font-size:0.82rem; padding:0.3rem 0.6rem; border-radius:0.4rem;
                       border:1px solid var(--tp-border); background:var(--tp-tbl-head);
                       color:var(--tp-text); outline:none; cursor:pointer; }
    .tp-custom-input:focus { border-color:var(--tp-accent); }
    .tp-custom-arrow { color:var(--tp-text-muted); font-size:0.9rem; }

    /* ── KPI cards ── */
    .tp-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:0.875rem; margin-bottom:1rem; }
    .tp-kpi  { background:var(--tp-card); border:1px solid var(--tp-border); border-radius:0.75rem; padding:1rem 1.25rem; }
    .tp-kpi-label { font-size:0.72rem; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; color:var(--tp-text-muted); margin-bottom:0.35rem; }
    .tp-kpi-value { font-size:1.75rem; font-weight:800; color:var(--tp-text); line-height:1; }
    .tp-kpi-sub   { font-size:0.75rem; color:var(--tp-text-muted); margin-top:0.25rem; }
    .tp-kpi.accent  .tp-kpi-value { color:var(--tp-accent); }
    .tp-kpi.success .tp-kpi-value { color:var(--tp-success); }
    .tp-kpi.warning .tp-kpi-value { color:var(--tp-warning); }
    .tp-kpi.danger  .tp-kpi-value { color:var(--tp-danger); }

    /* ── Top performer ── */
    .tp-spotlight { background:linear-gradient(135deg, light-dark(#eff6ff,#0d1926), light-dark(#dbeafe,#0f2133)); border:1px solid light-dark(#bfdbfe,#1e3a5f); border-radius:0.75rem; padding:1rem 1.25rem; display:flex; align-items:center; gap:1rem; margin-bottom:1rem; }
    .tp-spotlight-badge { background:var(--tp-accent); color:#fff; border-radius:9999px; width:2.75rem; height:2.75rem; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1rem; flex-shrink:0; }
    .tp-spotlight-eyebrow { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--tp-accent); margin-bottom:0.15rem; }
    .tp-spotlight-name  { font-size:1rem; font-weight:700; color:var(--tp-text); }
    .tp-spotlight-meta  { font-size:0.8rem; color:var(--tp-text-muted); margin-top:0.1rem; }
    .tp-trophy { font-size:1.5rem; margin-left:auto; }

    /* ── Team table ── */
    .tp-tbl-wrap { overflow-x:auto; }
    .tp-tbl      { width:100%; border-collapse:collapse; font-size:0.82rem; }
    .tp-tbl thead tr { background:var(--tp-tbl-head); }
    .tp-tbl th   { padding:0.5rem 0.75rem; font-size:0.75rem; font-weight:700; color:var(--tp-text-muted); border-bottom:1px solid var(--tp-border); text-align:left; white-space:nowrap; }
    .tp-tbl th.num { text-align:center; }
    .tp-tbl tbody tr { border-bottom:1px solid var(--tp-border); transition:background .1s; }
    .tp-tbl tbody tr:hover { background:var(--tp-row-hover); }
    .tp-tbl tbody tr:last-child { border-bottom:none; }
    .tp-tbl td   { padding:0.55rem 0.75rem; color:var(--tp-text-sec); vertical-align:middle; }
    .tp-tbl td.num { text-align:center; font-variant-numeric:tabular-nums; }

    /* ── User cell ── */
    .tp-user-cell { display:flex; align-items:center; gap:0.6rem; }
    .tp-avatar    { width:1.85rem; height:1.85rem; border-radius:9999px; background:var(--tp-accent); color:#fff; font-size:0.7rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .tp-level-indent { display:inline-block; }
    .tp-user-name { font-weight:600; color:var(--tp-text); }
    .tp-level-chip { font-size:0.65rem; font-weight:600; padding:0.1rem 0.45rem; border-radius:9999px; background:var(--tp-tbl-head); color:var(--tp-text-muted); margin-left:0.35rem; }

    /* ── Activity dot ── */
    .tp-dot { width:9px; height:9px; border-radius:9999px; display:inline-block; flex-shrink:0; }
    .tp-dot.green  { background:#16a34a; box-shadow:0 0 0 2px light-dark(#dcfce7,#052e16); }
    .tp-dot.yellow { background:#d97706; box-shadow:0 0 0 2px light-dark(#fef9c3,#1c1a0e); }
    .tp-dot.red    { background:#dc2626; box-shadow:0 0 0 2px light-dark(#fee2e2,#1c0a0a); }
    .tp-dot.none   { background:var(--tp-border); }

    /* ── Status pills ── */
    .tp-pill { display:inline-flex; align-items:center; justify-content:center; min-width:1.5rem; padding:0.1rem 0.4rem; border-radius:9999px; font-size:0.75rem; font-weight:700; }
    .tp-pill.draft   { background:light-dark(#f3f4f6,#27272a); color:light-dark(#374151,#9ca3af); }
    .tp-pill.pending { background:light-dark(#fef9c3,#1c1a0e); color:light-dark(#854d0e,#fbbf24); }
    .tp-pill.approved{ background:light-dark(#dcfce7,#052e16); color:light-dark(#166534,#86efac); }
    .tp-pill.rejected{ background:light-dark(#fee2e2,#1c0a0a); color:light-dark(#991b1b,#fca5a5); }

    /* ── Trend ── */
    .tp-trend { display:inline-flex; align-items:center; gap:0.2rem; font-size:0.75rem; font-weight:700; }
    .tp-trend.up   { color:var(--tp-success); }
    .tp-trend.down { color:var(--tp-danger); }
    .tp-trend.flat { color:var(--tp-text-muted); }

    /* ── Completion bar ── */
    .tp-completion { display:flex; align-items:center; gap:0.5rem; }
    .tp-bar-track  { flex:1; height:5px; border-radius:9999px; background:var(--tp-border); overflow:hidden; min-width:40px; }
    .tp-bar-fill   { height:100%; border-radius:9999px; background:var(--tp-accent); transition:width .4s ease; }
    .tp-bar-pct    { font-size:0.72rem; font-weight:600; color:var(--tp-text-muted); min-width:2rem; text-align:right; }

    /* ── Charts grid ── */
    .tp-charts { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
    @media(max-width:768px) { .tp-charts { grid-template-columns:1fr; } }

    /* ── Reinsurer bars ── */
    .tp-reinsbars { display:flex; flex-direction:column; gap:0.65rem; }
    .tp-reinsbar-row   { display:flex; flex-direction:column; gap:0.2rem; }
    .tp-reinsbar-label { display:flex; justify-content:space-between; font-size:0.78rem; color:var(--tp-text-sec); }
    .tp-reinsbar-name  { font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70%; }
    .tp-reinsbar-count { font-weight:700; color:var(--tp-text); }
    .tp-reinsbar-track { height:6px; border-radius:9999px; background:var(--tp-border); overflow:hidden; }
    .tp-reinsbar-fill  { height:100%; border-radius:9999px; background:var(--tp-accent); transition:width .5s ease; }

    /* ── Monthly bar chart ── */
    .tp-monthly-chart { display:flex; align-items:flex-end; gap:0.5rem; height:120px; padding-bottom:1.5rem; position:relative; }
    .tp-monthly-col   { display:flex; flex-direction:column; align-items:center; gap:0.3rem; flex:1; height:100%; justify-content:flex-end; }
    .tp-monthly-bar   { width:100%; border-radius:4px 4px 0 0; background:var(--tp-accent); opacity:0.85; min-height:3px; transition:height .4s ease; }
    .tp-monthly-val   { font-size:0.68rem; font-weight:700; color:var(--tp-text-muted); }
    .tp-monthly-lbl   { font-size:0.68rem; color:var(--tp-text-muted); position:absolute; bottom:0; }
    .tp-monthly-labels{ display:flex; gap:0.5rem; margin-top:0.35rem; }
    .tp-monthly-label-item { flex:1; text-align:center; font-size:0.7rem; color:var(--tp-text-muted); }

    /* ── Pending reviews ── */
    .tp-pending-list { display:flex; flex-direction:column; gap:0; }
    .tp-pending-row  { display:flex; align-items:center; gap:1rem; padding:0.65rem 0; border-bottom:1px solid var(--tp-border); flex-wrap:wrap; }
    .tp-pending-row:last-child { border-bottom:none; }
    .tp-pending-code { font-size:0.8rem; font-weight:700; color:var(--tp-accent); white-space:nowrap; min-width:100px; }
    .tp-pending-info { flex:1; min-width:0; }
    .tp-pending-desc { font-size:0.82rem; font-weight:500; color:var(--tp-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .tp-pending-meta { font-size:0.75rem; color:var(--tp-text-muted); margin-top:0.1rem; }
    .tp-pending-days { font-size:0.75rem; color:var(--tp-text-muted); white-space:nowrap; }
    .tp-btn-review   { display:inline-flex; align-items:center; gap:0.35rem; font-size:0.78rem; font-weight:600; padding:0.3rem 0.75rem; border-radius:0.4rem; background:var(--tp-accent); color:#fff; text-decoration:none; transition:background .12s; white-space:nowrap; }
    .tp-btn-review:hover { background:#3290af; }

    /* ── Empty states ── */
    .tp-empty { text-align:center; padding:2.5rem 1rem; color:var(--tp-text-muted); font-size:0.875rem; }
    .tp-empty svg { margin:0 auto 0.75rem; opacity:0.3; }

    /* ── Reinsurer × Team matrix ── */
    .tp-matrix-wrap  { overflow-x:auto; }
    .tp-matrix       { width:100%; border-collapse:collapse; font-size:0.8rem; }
    .tp-matrix th    { padding:0.5rem 0.75rem; font-size:0.72rem; font-weight:700; color:var(--tp-text-muted); background:var(--tp-tbl-head); border-bottom:1px solid var(--tp-border); text-align:center; white-space:nowrap; }
    .tp-matrix th.left { text-align:left; min-width:160px; }
    .tp-matrix tbody tr { border-bottom:1px solid var(--tp-border); transition:background .1s; }
    .tp-matrix tbody tr:last-child { border-bottom:none; }
    .tp-matrix tbody tr:hover { background:var(--tp-row-hover); }
    .tp-matrix td    { padding:0.45rem 0.75rem; text-align:center; vertical-align:middle; font-variant-numeric:tabular-nums; }
    .tp-matrix td.rein-name { text-align:left; font-weight:500; color:var(--tp-text-sec); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }
    .tp-matrix td.rein-total { font-weight:800; color:var(--tp-text); }
    /* heat cells */
    .tp-cell-val { display:inline-flex; align-items:center; justify-content:center; min-width:1.75rem; height:1.6rem; border-radius:0.35rem; font-weight:600; font-size:0.78rem; padding:0 0.3rem; }
    .tp-cell-val.zero  { color:var(--tp-text-muted); background:transparent; }
    .tp-cell-val.low   { background:light-dark(#e0f2fe,#1a3a4f); color:light-dark(#0369a1,#60c4e8); }
    .tp-cell-val.mid   { background:light-dark(#bae6fd,#1a4f6e); color:light-dark(#0284c7,#7dd3fc); }
    .tp-cell-val.high  { background:light-dark(#41A2C3,#41A2C3); color:#fff; }
    /* user avatar chip in header */
    .tp-matrix-avatar { display:inline-flex; align-items:center; justify-content:center; width:1.6rem; height:1.6rem; border-radius:9999px; background:var(--tp-accent); color:#fff; font-size:0.65rem; font-weight:700; margin-bottom:0.2rem; }
    .tp-matrix-user  { display:flex; flex-direction:column; align-items:center; gap:0.1rem; }
</style>

@php
    $hasTeam      = $this->hasTeam();
    $kpis         = $this->getKpis();
    $members      = $hasTeam ? $this->getTeamMembers() : [];
    $topPerformer = $hasTeam ? $this->getTopPerformer() : null;
    $reinsurers   = $hasTeam ? $this->getReinsurerDistribution() : [];
    $monthly      = $this->getMonthlyTrend();
    $pending      = $hasTeam ? $this->getPendingReviews() : [];
    $matrix       = $hasTeam ? $this->getReinsurerTeamMatrix() : ['members' => [], 'rows' => []];
    $periodLabel  = $this->getPeriodLabel();
@endphp

{{-- Period filter --}}
<div class="tp-filter-bar">
    {{-- Quick presets --}}
    <button wire:click="$set('period','month')"   class="tp-period-btn {{ $period === 'month'   ? 'active' : '' }}">This Month</button>
    <button wire:click="$set('period','year')"    class="tp-period-btn {{ $period === 'year'    ? 'active' : '' }}">This Year</button>
    <button wire:click="$set('period','all')"     class="tp-period-btn {{ $period === 'all'     ? 'active' : '' }}">All Time</button>

    {{-- Divider --}}
    <span class="tp-period-divider"></span>

    {{-- Quarter buttons --}}
    @foreach([1,2,3,4] as $q)
    <button wire:click="setQuarter({{ $q }})"
            class="tp-period-btn quarter {{ $period === 'quarter' && $quarter === $q ? 'active' : '' }}">
        Q{{ $q }}
    </button>
    @endforeach

    {{-- Divider --}}
    <span class="tp-period-divider"></span>

    {{-- Custom range toggle --}}
    <button wire:click="$set('period','custom')"
            class="tp-period-btn {{ $period === 'custom' ? 'active' : '' }}">
        <svg style="width:12px;height:12px;display:inline;vertical-align:-1px;margin-right:3px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Custom
    </button>

    <span class="tp-period-label">{{ $periodLabel }}</span>
</div>

{{-- Custom date range panel --}}
@if($period === 'custom')
<div class="tp-custom-panel">
    <span class="tp-custom-label">From</span>
    <input type="month" class="tp-custom-input"
           wire:model.live="dateFrom"
           value="{{ $dateFrom }}"
           max="{{ $dateTo ?: now()->format('Y-m') }}">
    <span class="tp-custom-arrow">→</span>
    <span class="tp-custom-label">To</span>
    <input type="month" class="tp-custom-input"
           wire:model.live="dateTo"
           value="{{ $dateTo }}"
           min="{{ $dateFrom }}"
           max="{{ now()->format('Y-m') }}">
    @if($dateFrom || $dateTo)
        <button wire:click="$set('dateFrom',null); $set('dateTo',null)"
                style="font-size:0.75rem; color:var(--tp-text-muted); background:none; border:none; cursor:pointer; margin-left:0.25rem;">
            ✕ Clear
        </button>
    @endif
    <span style="font-size:0.75rem; color:var(--tp-text-muted); margin-left:auto;">
        {{ $dateFrom && $dateTo ? 'Showing: '.$periodLabel : 'Select a start and end month' }}
    </span>
</div>
@endif

@if(! $hasTeam)
    {{-- No team assigned --}}
    <div class="tp-section">
        <div class="tp-empty">
            <svg style="width:40px;height:40px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197"/>
            </svg>
            <div style="font-weight:600; color:var(--tp-text); margin-bottom:0.25rem;">No team members assigned</div>
            <div>You don't have any direct reports yet. Assign managers in the <strong>Users</strong> section to start tracking team performance.</div>
        </div>
    </div>
@else

    {{-- KPI Cards --}}
    <div class="tp-kpis">
        <div class="tp-kpi accent">
            <div class="tp-kpi-label">Team Members</div>
            <div class="tp-kpi-value">{{ $kpis['team_count'] }}</div>
            <div class="tp-kpi-sub">Across all hierarchy levels</div>
        </div>
        <div class="tp-kpi {{ $kpis['pending_review'] > 0 ? 'warning' : '' }}">
            <div class="tp-kpi-label">Pending My Review</div>
            <div class="tp-kpi-value">{{ $kpis['pending_review'] }}</div>
            <div class="tp-kpi-sub">Awaiting your approval</div>
        </div>
        <div class="tp-kpi">
            <div class="tp-kpi-label">Registered {{ $period === 'month' ? 'This Month' : ($period === 'year' ? 'This Year' : 'All Time') }}</div>
            <div class="tp-kpi-value">{{ $kpis['registered'] }}</div>
            <div class="tp-kpi-sub">By all team members</div>
        </div>
        <div class="tp-kpi success">
            <div class="tp-kpi-label">Approved {{ $period === 'month' ? 'This Month' : ($period === 'year' ? 'This Year' : 'All Time') }}</div>
            <div class="tp-kpi-value">{{ $kpis['approved'] }}</div>
            <div class="tp-kpi-sub">
                @if($kpis['registered'] > 0)
                    {{ round(($kpis['approved'] / $kpis['registered']) * 100) }}% approval rate
                @else
                    No registrations yet
                @endif
            </div>
        </div>
    </div>

    {{-- Top Performer spotlight --}}
    @if($topPerformer)
    <div class="tp-spotlight">
        <div class="tp-spotlight-badge">{{ $topPerformer['initials'] }}</div>
        <div>
            <div class="tp-spotlight-eyebrow">🏆 Top Performer — {{ $periodLabel }}</div>
            <div class="tp-spotlight-name">{{ $topPerformer['name'] }}</div>
            <div class="tp-spotlight-meta">{{ $topPerformer['total'] }} {{ Str::plural('business', $topPerformer['total']) }} registered</div>
        </div>
    </div>
    @endif

    {{-- Team Performance Table --}}
    <div class="tp-section" style="margin-bottom:1rem;">
        <div class="tp-sec-header">
            <div class="tp-sec-title">
                <svg style="width:18px;height:18px;flex-shrink:0;color:var(--tp-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                Team Performance
            </div>
            <span style="font-size:0.75rem; color:var(--tp-text-muted);">
                <span class="tp-dot green" style="display:inline-block;margin-right:3px;"></span> Active ≤7d &nbsp;
                <span class="tp-dot yellow" style="display:inline-block;margin-right:3px;"></span> Active ≤30d &nbsp;
                <span class="tp-dot red" style="display:inline-block;margin-right:3px;"></span> Inactive >30d
            </span>
        </div>
        <div class="tp-tbl-wrap">
            <table class="tp-tbl">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Team Member</th>
                        <th class="num">Draft</th>
                        <th class="num">Pending</th>
                        <th class="num">Approved</th>
                        <th class="num">Revision</th>
                        <th class="num">Total</th>
                        <th style="min-width:110px;">Completion</th>
                        @if($this->hasPrevPeriod())<th>Trend</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                    <tr>
                        <td style="text-align:center;">
                            <span class="tp-dot {{ $m['activity_status'] }}" title="{{ $m['days_since'] !== null ? $m['days_since'].' days ago' : 'No activity' }}"></span>
                        </td>
                        <td>
                            <div class="tp-user-cell">
                                @if($m['level'] > 1)
                                    <span class="tp-level-indent" style="width:{{ ($m['level'] - 1) * 16 }}px; flex-shrink:0;"></span>
                                    <span style="color:var(--tp-border);margin-right:4px;font-size:0.8rem;">└</span>
                                @endif
                                <div class="tp-avatar">{{ $m['initials'] }}</div>
                                <div>
                                    <span class="tp-user-name">{{ $m['name'] }}</span>
                                    <span class="tp-level-chip">L{{ $m['level'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="num"><span class="tp-pill draft">{{ $m['draft'] }}</span></td>
                        <td class="num"><span class="tp-pill pending">{{ $m['pending'] }}</span></td>
                        <td class="num"><span class="tp-pill approved">{{ $m['approved'] }}</span></td>
                        <td class="num"><span class="tp-pill rejected">{{ $m['rejected'] }}</span></td>
                        <td class="num" style="font-weight:700; color:var(--tp-text);">{{ $m['total'] }}</td>
                        <td>
                            <div class="tp-completion">
                                <div class="tp-bar-track">
                                    <div class="tp-bar-fill" style="width:{{ $m['completion'] }}%;"></div>
                                </div>
                                <span class="tp-bar-pct">{{ $m['completion'] }}%</span>
                            </div>
                        </td>
                        @if($this->hasPrevPeriod())
                        <td>
                            @if($m['trend'])
                                <span class="tp-trend {{ $m['trend']['dir'] }}">
                                    @if($m['trend']['dir'] === 'up') ↑
                                    @elseif($m['trend']['dir'] === 'down') ↓
                                    @else →
                                    @endif
                                    {{ $m['trend']['pct'] }}%
                                </span>
                            @else
                                <span class="tp-trend flat">—</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="tp-empty">No team members found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="tp-charts">

        {{-- Reinsurer distribution --}}
        <div class="tp-section" style="margin-bottom:0;">
            <div class="tp-sec-header">
                <div class="tp-sec-title">
                    <svg style="width:15px;height:15px;color:var(--tp-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    Businesses by Reinsurer
                </div>
            </div>
            <div class="tp-sec-body">
                @if(empty($reinsurers))
                    <div class="tp-empty" style="padding:1.5rem;">No data for this period.</div>
                @else
                    <div class="tp-reinsbars">
                        @foreach($reinsurers as $r)
                        <div class="tp-reinsbar-row">
                            <div class="tp-reinsbar-label">
                                <span class="tp-reinsbar-name" title="{{ $r['name'] }}">{{ $r['name'] }}</span>
                                <span class="tp-reinsbar-count">{{ $r['total'] }}</span>
                            </div>
                            <div class="tp-reinsbar-track">
                                <div class="tp-reinsbar-fill" style="width:{{ $r['pct'] }}%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Monthly trend --}}
        <div class="tp-section" style="margin-bottom:0;">
            <div class="tp-sec-header">
                <div class="tp-sec-title">
                    <svg style="width:15px;height:15px;color:var(--tp-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/></svg>
                    Team Registrations — Last 6 Months
                </div>
            </div>
            <div class="tp-sec-body">
                <div class="tp-monthly-chart">
                    @foreach($monthly as $m)
                    <div class="tp-monthly-col">
                        <div class="tp-monthly-val">{{ $m['count'] > 0 ? $m['count'] : '' }}</div>
                        <div class="tp-monthly-bar" style="height:{{ max($m['pct'], $m['count'] > 0 ? 6 : 3) }}%;"></div>
                    </div>
                    @endforeach
                </div>
                <div class="tp-monthly-labels">
                    @foreach($monthly as $m)
                        <div class="tp-monthly-label-item">{{ $m['label'] }}</div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- Reinsurer × Team matrix --}}
    @if(count($matrix['rows']) > 0)
    <div class="tp-section">
        <div class="tp-sec-header">
            <div class="tp-sec-title">
                <svg style="width:15px;height:15px;color:var(--tp-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                Businesses by Reinsurer × Team Member
            </div>
            <span style="font-size:0.72rem; color:var(--tp-text-muted);">
                <span style="display:inline-flex;align-items:center;gap:0.3rem;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:light-dark(#41A2C3,#41A2C3);"></span> High &nbsp;
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:light-dark(#bae6fd,#0d2133);"></span> Mid &nbsp;
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:light-dark(#e0f2fe,#0c1a24);"></span> Low
                </span>
            </span>
        </div>
        <div class="tp-matrix-wrap">
            <table class="tp-matrix">
                <thead>
                    <tr>
                        <th class="left">Reinsurer</th>
                        @foreach($matrix['members'] as $mu)
                        <th>
                            <div class="tp-matrix-user">
                                <div class="tp-matrix-avatar">{{ $mu['initials'] }}</div>
                                <span style="font-size:0.65rem; max-width:70px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block;" title="{{ $mu['name'] }}">
                                    {{ explode(' ', $mu['name'])[0] }}
                                </span>
                            </div>
                        </th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matrix['rows'] as $row)
                    @php
                        $rowMax = max(array_values($row['counts']) ?: [1]);
                    @endphp
                    <tr>
                        <td class="rein-name" title="{{ $row['reinsurer'] }}">{{ $row['reinsurer'] }}</td>
                        @foreach($matrix['members'] as $mu)
                        @php
                            $cnt = $row['counts'][$mu['id']] ?? 0;
                            $heatClass = match(true) {
                                $cnt === 0              => 'zero',
                                $cnt < $rowMax * 0.4    => 'low',
                                $cnt < $rowMax * 0.75   => 'mid',
                                default                 => 'high',
                            };
                        @endphp
                        <td>
                            <span class="tp-cell-val {{ $heatClass }}">
                                {{ $cnt > 0 ? $cnt : '—' }}
                            </span>
                        </td>
                        @endforeach
                        <td class="rein-total">{{ $row['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--tp-border); background:var(--tp-tbl-head);">
                        <td style="padding:0.5rem 0.75rem; font-size:0.75rem; font-weight:700; color:var(--tp-text-muted); text-transform:uppercase; letter-spacing:0.04em;">Total</td>
                        @foreach($matrix['members'] as $mu)
                        @php
                            $colTotal = collect($matrix['rows'])->sum(fn($r) => $r['counts'][$mu['id']] ?? 0);
                        @endphp
                        <td style="padding:0.5rem 0.75rem; text-align:center; font-weight:800; color:var(--tp-text); font-variant-numeric:tabular-nums;">
                            {{ $colTotal > 0 ? $colTotal : '—' }}
                        </td>
                        @endforeach
                        <td style="padding:0.5rem 0.75rem; text-align:center; font-weight:800; color:var(--tp-accent); font-variant-numeric:tabular-nums;">
                            {{ collect($matrix['rows'])->sum('total') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- Pending Reviews queue --}}
    @if(count($pending) > 0)
    <div class="tp-section">
        <div class="tp-sec-header">
            <div class="tp-sec-title">
                <svg style="width:15px;height:15px;color:var(--tp-warning);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pending My Review
            </div>
            <span style="font-size:0.75rem; color:var(--tp-warning); font-weight:600;">
                {{ count($pending) }} awaiting action
            </span>
        </div>
        <div class="tp-sec-body" style="padding-top:0.25rem; padding-bottom:0.25rem;">
            <div class="tp-pending-list">
                @foreach($pending as $p)
                <div class="tp-pending-row">
                    <div class="tp-pending-code">{{ $p['business_code'] }}</div>
                    <div class="tp-pending-info">
                        <div class="tp-pending-desc" title="{{ $p['description'] }}">{{ $p['description'] }}</div>
                        <div class="tp-pending-meta">{{ $p['submitter'] }} · {{ $p['reinsurer'] }}</div>
                    </div>
                    <div class="tp-pending-days">
                        @if($p['days_ago'] !== null)
                            {{ $p['days_ago'] === 0 ? 'Today' : $p['days_ago'].' '.Str::plural('day', $p['days_ago']).' ago' }}
                        @endif
                    </div>
                    <a href="{{ $p['edit_url'] }}" class="tp-btn-review">
                        <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Review
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

@endif
</div>

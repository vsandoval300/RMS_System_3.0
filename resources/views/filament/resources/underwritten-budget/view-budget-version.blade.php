<x-filament-panels::page>

    @php
        $months      = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
        $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $items       = $budget->items->sortBy('reinsurer_id');
        $grandTotal  = $items->sum('premium_budget');
        $count       = $items->count();
    @endphp

    <style>
        /* ── Semantic tokens ── */
        .view-card {
            --bc-card:           light-dark(var(--color-white),       #18181B);
            --bc-border:         light-dark(var(--color-gray-200),    var(--color-gray-700));
            --bc-border-input:   light-dark(var(--color-gray-300),    var(--color-gray-600));
            --bc-text:           light-dark(var(--color-gray-950),    var(--color-white));
            --bc-text-sec:       light-dark(var(--color-gray-700),    var(--color-gray-200));
            --bc-text-muted:     light-dark(var(--color-gray-500),    var(--color-gray-400));
            --bc-row-hover:      light-dark(var(--color-gray-50),     var(--color-gray-700));
            --bc-sunken:         light-dark(var(--color-gray-100),    var(--color-gray-700));
            --bc-primary-subtle: light-dark(var(--color-primary-50),  var(--color-primary-950));
            --bc-primary-text:   light-dark(var(--color-primary-700), var(--color-primary-300));
        }

        .view-card {
            background: var(--bc-card);
            border: 1px solid var(--bc-border);
            border-radius: 0.75rem; overflow: hidden;
        }
        .view-header {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--bc-border);
            display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;
        }
        .view-meta-label {
            font-size: 0.875rem; font-weight: 600;
            color: var(--bc-text-muted); margin-bottom: 0.3rem;
        }
        .view-meta-value { font-size: 0.925rem; font-weight: 500; color: var(--bc-text); }
        .version-badge, .year-badge {
            display: inline-flex; align-items: center;
            background: #41A2C3; color: #fff;
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem; border: none;
        }
        .view-notes {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--bc-border);
            font-size: 0.875rem; color: var(--bc-text-sec); font-style: italic;
        }
        .view-table-wrap { overflow-x: auto; }
        .view-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }

        .view-table thead tr { background: light-dark(var(--color-gray-50), #1D1D20); }
        .view-table th {
            padding: 0.55rem 0.5rem; text-align: center;
            font-size: 0.82rem; font-weight: 600;
            color: light-dark(var(--color-gray-700), var(--color-gray-200));
            border-bottom: 1px solid var(--bc-border); white-space: nowrap;
        }
        .view-table th.col-id    { width: 3rem; text-align: right; }
        .view-table th.col-name  { text-align: left; min-width: 10rem; padding-left: 0.75rem; }
        .view-table th.col-month { width: 5.5rem; }
        .view-table th.col-total { text-align: right; min-width: 7rem;
                                   background: var(--bc-primary-subtle); color: var(--bc-primary-text); }
        .view-table th.col-pct   { text-align: right; min-width: 5rem; }

        .view-table tbody tr { border-bottom: 1px solid var(--bc-border); transition: background 0.1s; }
        .view-table tbody tr:hover { background: var(--bc-row-hover); }
        .view-table td { padding: 0.45rem 0.5rem; color: var(--bc-text-sec); vertical-align: middle; }
        .view-table td.col-id    { text-align: right; color: var(--bc-text-muted); font-size: 0.75rem; font-variant-numeric: tabular-nums; }
        .view-table td.col-name  { font-weight: 500; color: var(--bc-text); padding-left: 0.75rem; }
        .view-table td.col-month { text-align: right; font-variant-numeric: tabular-nums; color: var(--bc-text-muted); }
        .view-table td.col-month.has-val { color: var(--bc-text-sec); }
        .view-table td.col-total { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600;
                                   background: var(--bc-primary-subtle); color: var(--bc-primary-text); }
        .view-table td.col-pct   { text-align: right; color: var(--bc-text-muted); font-size: 0.75rem; }

        .view-table tfoot tr { background: light-dark(var(--color-gray-50), #1D1D20); border-top: 2px solid var(--bc-border); }
        .view-table tfoot td { padding: 0.6rem 0.5rem; font-weight: 700; color: var(--bc-text); font-variant-numeric: tabular-nums; }
        .view-table tfoot td.col-month-sum { text-align: right; font-size: 0.78rem; }
        .view-table tfoot td.col-total-sum { text-align: right;
                                             background: var(--bc-primary-subtle); color: var(--bc-primary-text); }

        .view-footer {
            padding: 0.9rem 1.25rem; border-top: 1px solid var(--bc-border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 0.75rem;
        }
        .view-footer-info { font-size: 0.80rem; color: var(--bc-text-muted); }
        .view-footer-info span { font-weight: 700; color: var(--bc-text-sec); }
        .view-total { font-size: 0.925rem; font-weight: 700; color: var(--bc-text); font-variant-numeric: tabular-nums; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: light-dark(var(--color-gray-100), #09090B); color: var(--bc-text-sec);
            border: 1px solid var(--bc-border-input);
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem; text-decoration: none; transition: background 0.12s;
        }
        .btn-back:hover { background: var(--bc-border); }
        .btn-edit {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #41A2C3; color: #fff; border: none;
            border-radius: 0.45rem; font-size: 0.875rem; font-weight: 600;
            padding: 0.45rem 0.85rem; text-decoration: none; transition: background 0.12s;
        }
        .btn-edit:hover { background: #3290af; }
    </style>

    <div class="view-card">

        {{-- Header meta ──────────────────────────────────── --}}
        <div class="view-header">
            <div>
                <div class="view-meta-label">Year</div>
                <div class="year-badge">{{ $budget->year }}</div>
            </div>
            <div>
                <div class="view-meta-label">Version</div>
                <div class="version-badge">v{{ $budget->version }}</div>
            </div>
            <div>
                <div class="view-meta-label">Version Label</div>
                <div class="view-meta-value">{{ $budget->label }}</div>
            </div>
            <div>
                <div class="view-meta-label">Created by</div>
                <div class="view-meta-value">{{ $budget->creator?->name ?? '—' }}</div>
            </div>
            <div>
                <div class="view-meta-label">Date</div>
                <div class="view-meta-value">{{ $budget->created_at?->format('M d, Y') ?? '—' }}</div>
            </div>
            <div>
                <div class="view-meta-label">Total Budget</div>
                <div class="view-total">${{ number_format($grandTotal, 2) }}</div>
            </div>
        </div>

        {{-- Notes ───────────────────────────────────────── --}}
        @if($budget->notes)
            <div class="view-notes">
                <span style="font-size:0.70rem; font-weight:600; letter-spacing:0.07em; text-transform:uppercase; color:var(--bc-text-muted); font-style:normal;">Notes: </span>
                {{ $budget->notes }}
            </div>
        @endif

        {{-- Items table ──────────────────────────────────── --}}
        <div class="view-table-wrap">
            <table class="view-table">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th class="col-name">Reinsurer</th>
                        @foreach($monthLabels as $i => $label)
                            <th class="col-month">
                                <span style="font-size:0.72rem; display:block; color:var(--bc-text-muted);">
                                    {{ sprintf('%04d%02d', $budget->year, $i + 1) }}
                                </span>
                                {{ $label }}
                            </th>
                        @endforeach
                        <th class="col-total">Total</th>
                        <th class="col-pct">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $rowTotal = $item->month_total;
                            $pct      = $grandTotal > 0 ? ($rowTotal / $grandTotal) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="col-id">{{ $item->reinsurer_id }}</td>
                            <td class="col-name">{{ $item->reinsurer?->name ?? '—' }}</td>
                            @foreach($months as $mk)
                                @php $mv = (float) $item->$mk; @endphp
                                <td class="col-month {{ $mv > 0 ? 'has-val' : '' }}">
                                    {{ $mv > 0 ? number_format($mv, 2) : '—' }}
                                </td>
                            @endforeach
                            <td class="col-total">${{ number_format($rowTotal, 2) }}</td>
                            <td class="col-pct">{{ number_format($pct, 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($months) + 1 }}"
                                style="text-align:center; padding:2rem; color:var(--bc-text-muted)">
                                No items found for this budget version.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($items->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="2" class="col-name" style="padding-left:0.75rem; font-size:0.72rem; letter-spacing:0.05em; text-transform:uppercase;">
                            Total
                        </td>
                        @foreach($months as $mk)
                            @php $colSum = $items->sum(fn($i) => (float) $i->$mk); @endphp
                            <td class="col-month-sum">
                                {{ $colSum > 0 ? number_format($colSum, 2) : '—' }}
                            </td>
                        @endforeach
                        <td class="col-total-sum">${{ number_format($grandTotal, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Footer ──────────────────────────────────────── --}}
        <div class="view-footer">
            <div class="view-footer-info">
                <span>{{ $count }}</span> {{ $count === 1 ? 'reinsurer' : 'reinsurers' }}
                &nbsp;·&nbsp;
                Total: <span class="view-total">${{ number_format($grandTotal, 2) }}</span>
            </div>
            <div style="display:flex; gap:0.6rem; align-items:center;">
                <a href="{{ \App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource::getUrl('index') }}"
                   class="btn-back">← Back</a>
                <a href="{{ \App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource::getUrl('edit-version', ['record' => $budget]) }}"
                   class="btn-edit">Edit</a>
            </div>
        </div>
    </div>

</x-filament-panels::page>

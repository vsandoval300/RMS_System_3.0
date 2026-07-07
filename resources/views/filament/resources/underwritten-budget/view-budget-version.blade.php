<x-filament-panels::page>

    @php
        $months      = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
        $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $items       = $budget->items->sortBy('reinsurer_id');
        $grandTotal  = $items->sum('premium_budget');
        $count       = $items->count();
    @endphp

    <style>
        .view-card {
            background: light-dark(#ffffff, #1e2535);
            border: 1px solid light-dark(#e5e7eb, #2d3748);
            border-radius: 0.75rem; overflow: hidden;
        }
        .view-header {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid light-dark(#e5e7eb, #2d3748);
            display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;
        }
        .view-meta-label {
            font-size: 0.70rem; font-weight: 600; letter-spacing: 0.07em;
            text-transform: uppercase; color: light-dark(#6b7280, #9ca3af); margin-bottom: 0.3rem;
        }
        .view-meta-value { font-size: 0.925rem; font-weight: 500; color: light-dark(#111827, #f3f4f6); }
        .version-badge {
            display: inline-flex; align-items: center;
            background: light-dark(#ede9fe, #312e81); color: light-dark(#4f46e5, #a5b4fc);
            border-radius: 999px; font-size: 0.78rem; font-weight: 700;
            padding: 0.18rem 0.65rem; border: 1px solid light-dark(#c4b5fd, #4338ca);
        }
        .year-badge {
            display: inline-flex; align-items: center;
            background: light-dark(#dbeafe, #1e3a5f); color: light-dark(#1d4ed8, #93c5fd);
            border-radius: 999px; font-size: 0.78rem; font-weight: 700;
            padding: 0.18rem 0.65rem; border: 1px solid light-dark(#93c5fd, #1e40af);
        }
        .view-notes {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid light-dark(#e5e7eb, #2d3748);
            font-size: 0.875rem; color: light-dark(#374151, #d1d5db); font-style: italic;
        }
        .view-table-wrap { overflow-x: auto; }
        .view-table {
            width: 100%; border-collapse: collapse; font-size: 0.8rem;
        }
        .view-table thead tr { background: light-dark(#f3f4f6, #1a2236); }
        .view-table th {
            padding: 0.55rem 0.5rem; text-align: center;
            font-size: 0.65rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;
            color: light-dark(#6b7280, #9ca3af);
            border-bottom: 1px solid light-dark(#e5e7eb, #2d3748); white-space: nowrap;
        }
        .view-table th.col-id    { width: 3rem; text-align: right; }
        .view-table th.col-name  { text-align: left; min-width: 10rem; padding-left: 0.75rem; }
        .view-table th.col-month { width: 5.5rem; }
        .view-table th.col-total { text-align: right; min-width: 7rem; background: light-dark(#eef2ff, #1e2d55); }
        .view-table th.col-pct   { text-align: right; min-width: 5rem; }

        .view-table tbody tr {
            border-bottom: 1px solid light-dark(#f3f4f6, #1f2b3e); transition: background 0.1s;
        }
        .view-table tbody tr:hover { background: light-dark(#f9fafb, #1a2236); }
        .view-table td {
            padding: 0.45rem 0.5rem; color: light-dark(#374151, #d1d5db); vertical-align: middle;
        }
        .view-table td.col-id    { text-align: right; color: light-dark(#9ca3af, #6b7280); font-size: 0.75rem; font-variant-numeric: tabular-nums; }
        .view-table td.col-name  { font-weight: 500; color: light-dark(#111827, #f3f4f6); padding-left: 0.75rem; }
        .view-table td.col-month { text-align: right; font-variant-numeric: tabular-nums; color: light-dark(#6b7280, #9ca3af); }
        .view-table td.col-month.has-val { color: light-dark(#111827, #d1d5db); }
        .view-table td.col-total { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; background: light-dark(rgba(238,242,255,0.5), rgba(30,45,85,0.4)); }
        .view-table td.col-pct   { text-align: right; color: light-dark(#6b7280, #9ca3af); font-size: 0.75rem; }

        .view-table tfoot tr { background: light-dark(#f3f4f6, #1a2236); border-top: 2px solid light-dark(#e5e7eb, #2d3748); }
        .view-table tfoot td { padding: 0.6rem 0.5rem; font-weight: 700; color: light-dark(#111827, #f3f4f6); font-variant-numeric: tabular-nums; }
        .view-table tfoot td.col-month-sum { text-align: right; font-size: 0.78rem; }
        .view-table tfoot td.col-total-sum { text-align: right; background: light-dark(rgba(238,242,255,0.7), rgba(30,45,85,0.6)); }

        .view-footer {
            padding: 0.9rem 1.25rem; border-top: 1px solid light-dark(#e5e7eb, #2d3748);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 0.75rem;
        }
        .view-footer-info { font-size: 0.80rem; color: light-dark(#6b7280, #9ca3af); }
        .view-footer-info span { font-weight: 700; color: light-dark(#374151, #d1d5db); }
        .view-total { font-size: 0.925rem; font-weight: 700; color: light-dark(#111827, #f3f4f6); font-variant-numeric: tabular-nums; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: transparent; color: light-dark(#6b7280, #9ca3af);
            border: 1px solid light-dark(#d1d5db, #374151); border-radius: 0.55rem;
            font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem;
            text-decoration: none; transition: background 0.15s;
        }
        .btn-back:hover { background: light-dark(#f3f4f6, #1a2236); }
        .btn-edit {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #f59e0b; color: #fff; border: none; border-radius: 0.55rem;
            font-size: 0.875rem; font-weight: 600; padding: 0.5rem 1.1rem;
            text-decoration: none; transition: background 0.15s;
        }
        .btn-edit:hover { background: #d97706; }
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
                <span style="font-size:0.70rem; font-weight:600; letter-spacing:0.07em; text-transform:uppercase; color:light-dark(#6b7280,#9ca3af); font-style:normal;">Notes: </span>
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
                                <span style="font-size:0.58rem; display:block; color:light-dark(#9ca3af,#6b7280);">
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
                                style="text-align:center; padding:2rem; color:light-dark(#9ca3af,#6b7280)">
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

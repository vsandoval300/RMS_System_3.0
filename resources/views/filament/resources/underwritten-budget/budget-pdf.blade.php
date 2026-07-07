<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 7.5pt; color: #111827; background: #fff; }

    .page-header { padding: 10px 14px 8px; border-bottom: 2px solid #1e3a5f; display: flex; justify-content: space-between; align-items: flex-end; }
    .page-title  { font-size: 13pt; font-weight: 700; color: #1e3a5f; }
    .page-meta   { font-size: 7pt; color: #6b7280; text-align: right; line-height: 1.6; }
    .page-meta b { color: #374151; }

    .meta-row {
        display: flex; gap: 28px; padding: 7px 14px;
        background: #f3f4f6; border-bottom: 1px solid #e5e7eb; font-size: 7pt;
    }
    .meta-item label { font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; display: block; margin-bottom: 1px; }
    .meta-item span  { color: #111827; font-size: 8pt; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead tr { background: #1e3a5f; color: #ffffff; }
    thead th {
        padding: 5px 4px; font-size: 6.5pt; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.04em; white-space: nowrap; text-align: right;
        border: 1px solid #374151;
    }
    thead th.left { text-align: left; }
    thead th.center { text-align: center; }
    thead .sub { display: block; font-size: 5.5pt; font-weight: 400; color: #93c5fd; letter-spacing: 0; }

    tbody tr { border-bottom: 1px solid #f3f4f6; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tbody td { padding: 3.5px 4px; vertical-align: middle; }
    tbody td.num { text-align: right; font-variant-numeric: tabular-nums; }
    tbody td.id  { text-align: right; color: #9ca3af; font-size: 7pt; }
    tbody td.name { text-align: left; font-weight: 500; max-width: 110px; }
    tbody td.zero { color: #d1d5db; }
    tbody td.total-col {
        text-align: right; font-weight: 700; font-variant-numeric: tabular-nums;
        background: #eef2ff; color: #1e3a5f;
    }
    tbody td.pct { text-align: right; color: #6b7280; font-size: 7pt; }

    tfoot tr { background: #1e3a5f; color: #ffffff; }
    tfoot td { padding: 5px 4px; font-weight: 700; text-align: right; font-variant-numeric: tabular-nums; border-top: 2px solid #93c5fd; }
    tfoot td.left { text-align: left; font-size: 7pt; letter-spacing: 0.05em; text-transform: uppercase; }

    .footer { margin-top: 10px; padding-top: 5px; border-top: 1px solid #e5e7eb; font-size: 6.5pt; color: #9ca3af; display: flex; justify-content: space-between; }

    @page { size: A4 landscape; margin: 10mm 10mm 10mm 10mm; }
</style>
</head>
<body>

@php
    $months      = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
    $monthNames  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $items       = $budget->items->sortBy('reinsurer_id');
    $grandTotal  = $items->sum('premium_budget');
    $fmt = fn(float $v): string => number_format($v, 2);
@endphp

<div class="page-header">
    <div>
        <div class="page-title">Underwritten Budget</div>
        <div style="font-size:8.5pt; color:#374151; margin-top:2px;">{{ $budget->label }}</div>
    </div>
    <div class="page-meta">
        <b>Year:</b> {{ $budget->year }} &nbsp;·&nbsp;
        <b>Version:</b> v{{ $budget->version }} &nbsp;·&nbsp;
        <b>Reinsurers:</b> {{ $items->count() }}<br>
        <b>Created by:</b> {{ $budget->creator?->name ?? '—' }} &nbsp;·&nbsp;
        <b>Date:</b> {{ $budget->created_at?->format('M d, Y') ?? '—' }}<br>
        @if($budget->notes)
        <span style="font-style:italic;">Notes: {{ $budget->notes }}</span>
        @endif
    </div>
</div>

<table>
    <thead>
        <tr>
            <th class="left" style="width:22px;">#</th>
            <th class="left" style="width:110px;">Reinsurer</th>
            @foreach($monthNames as $i => $name)
            <th style="width:52px;">
                <span class="sub">{{ sprintf('%04d%02d', $budget->year, $i + 1) }}</span>
                {{ $name }}
            </th>
            @endforeach
            <th style="width:64px; background:#1a2e50;">Total (USD)</th>
            <th style="width:34px; background:#1a2e50;">%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        @php
            $rowTotal = $item->month_total;
            $pct      = $grandTotal > 0 ? ($rowTotal / $grandTotal) * 100 : 0;
        @endphp
        <tr>
            <td class="id">{{ $item->reinsurer_id }}</td>
            <td class="name">{{ $item->reinsurer?->name ?? '—' }}</td>
            @foreach($months as $mk)
            @php $mv = (float) $item->$mk; @endphp
            <td class="num {{ $mv == 0 ? 'zero' : '' }}">{{ $mv > 0 ? $fmt($mv) : '—' }}</td>
            @endforeach
            <td class="total-col">{{ $fmt($rowTotal) }}</td>
            <td class="pct">{{ number_format($pct, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td class="left">Total</td>
            @foreach($months as $mk)
            @php $colSum = (float) $items->sum(fn($i) => (float) $i->$mk); @endphp
            <td>{{ $colSum > 0 ? $fmt($colSum) : '—' }}</td>
            @endforeach
            <td style="background:#0f1f38;">{{ $fmt($grandTotal) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <span>RMS-System &nbsp;·&nbsp; Underwritten Budget Report</span>
    <span>Generated: {{ now()->format('M d, Y H:i') }}</span>
</div>

</body>
</html>

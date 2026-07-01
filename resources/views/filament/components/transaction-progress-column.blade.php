@php
    $record = $getRecord();

    $logs = $record->relationLoaded('logs')
        ? $record->logs->sortBy('index')->values()
        : $record->logs()->withoutTrashed()->orderBy('index')->get();

    $total     = $logs->count();
    $completed = $logs->filter(fn ($l) => trim((string) $l->status) === 'Completed')->count();
    $progress  = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

    $dotStyle = function (string $status): array {
        return match ($status) {
            'Completed'  => ['bg' => '#22c55e', 'border' => '#16a34a'],
            'In process' => ['bg' => '#f59e0b', 'border' => '#d97706'],
            default      => ['bg' => 'transparent', 'border' => '#9ca3af'],
        };
    };
@endphp

<div class="flex items-center gap-1 flex-wrap">

    @foreach ($logs as $log)
        @php $style = $dotStyle(trim((string) $log->status)); @endphp

        <div style="
            width: 14px;
            height: 14px;
            border-radius: 9999px;
            border: 2px solid {{ $style['border'] }};
            background-color: {{ $style['bg'] }};
            flex-shrink: 0;
        "></div>

        @if (! $loop->last)
            <span style="font-size:10px; color: light-dark(#9ca3af,#6b7280); line-height:1;">›</span>
        @endif
    @endforeach

    @if ($total > 0)
        <span style="font-size:11px; font-weight:500; color: light-dark(#374151,#d1d5db); margin-left:4px;">
            {{ $progress }}%
        </span>
    @endif

</div>

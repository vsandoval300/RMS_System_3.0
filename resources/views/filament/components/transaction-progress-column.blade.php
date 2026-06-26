@php
    $progress = $getRecord()->lifecycleProgressPercentage();

    $barColor = match (true) {
        $progress >= 100 => '#22c55e',
        $progress > 0 => '#65a30d',
        default => '#9ca3af',
    };
@endphp

<div class="flex items-center gap-2 min-w-[120px]">
    <div
        style="
            width: 90px;
            height: 8px;
            border-radius: 9999px;
            overflow: hidden;
            border: 1px solid light-dark(#d1d5db, #374151);
            background-color: light-dark(#f9fafb, #111827);
        "
    >
        <div
            style="
                width: {{ $progress }}%;
                height: 100%;
                background: {{ $barColor }};
            "
        ></div>
    </div>

    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
        {{ $progress }}%
    </span>
</div>
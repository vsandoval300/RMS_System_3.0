@php
    $progress = max(0, min(100, (int) ($progress ?? 0)));
@endphp

<div class="w-full" style="padding: 4px 0;">
    <div
        style="
            width: 100%;
            height: 26px;
            border-radius: 999px;
            overflow: hidden;
            background-color: light-dark(rgba(0,0,0,0.07), rgba(255,255,255,0.08));
        "
    >
        <div
            style="
                width: {{ $progress }}%;
                height: 100%;
                background: #65a30d;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 500;
                font-size: 12px;
                letter-spacing: 0.02em;
                transition: width .3s ease;
            "
        >
            {{ $progress > 0 ? $progress . '%' : '' }}
        </div>
    </div>
</div>
@php
    $progress = max(0, min(100, (int) ($progress ?? 0)));
@endphp

<div class="w-full">
    

    
    <div
        style="
            width: 100%;
            height: 36px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid light-dark(#d1d5db, #52525b);
            background-color: light-dark(#f9fafb, #18181b);
        "
    >
        <div
            style="
                width: {{ $progress }}%;
                height: 100%;
                background: #65a30d;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                font-size: 16px;
                transition: width .3s ease;
            "
        >
            {{ $progress > 0 ? $progress . '%' : '' }}
        </div>
    </div>
</div>
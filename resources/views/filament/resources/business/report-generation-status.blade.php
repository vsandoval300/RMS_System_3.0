@php
    $label = $this->getGeneratingReportLabel();
@endphp

@if($label)
    <div style="
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.1rem;
        border-radius: 0.75rem;
        background: light-dark(#eff6ff, #172554);
        border: 1px solid light-dark(#bfdbfe, #1e3a8a);
        color: light-dark(#1e3a8a, #bfdbfe);
        margin-bottom: 1rem;
    ">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="animation: rgs-spin 1s linear infinite; flex-shrink: 0;">
            <path d="M12 2a10 10 0 0 1 10 10" />
        </svg>
        <div>
            <div style="font-weight: 600; font-size: 0.9rem;">Generating your {{ $label }}…</div>
            <div style="font-size: 0.82rem; opacity: 0.85;">You'll get a notification with the download link when it's ready.</div>
        </div>
    </div>

    <style>
        @keyframes rgs-spin { to { transform: rotate(360deg); } }
    </style>
@endif

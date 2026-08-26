@php
    $label = $this->getGeneratingReportLabel();
@endphp

<div wire:poll.5s>
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
            <x-filament::loading-indicator class="h-[18px] w-[18px]" style="flex-shrink: 0;" />
            <div>
                <div style="font-weight: 600; font-size: 0.9rem;">Generating your {{ $label }}…</div>
                <div style="font-size: 0.82rem; opacity: 0.85;">You'll get a notification with the download link when it's ready.</div>
            </div>
        </div>
    @endif
</div>

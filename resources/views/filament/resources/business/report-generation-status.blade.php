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
            background: light-dark(rgba(65,162,195,0.10), rgba(65,162,195,0.15));
            border: 1px solid light-dark(rgba(65,162,195,0.35), rgba(65,162,195,0.4));
            color: light-dark(#1b6a85, #41A2C3);
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

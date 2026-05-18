<x-filament::section heading="Underwritten Overview">

<div class="max-w-2xl">
        {{ $this->form }}
    </div>

    <div
        class="fi-grid grid gap-6 mt-6"
        style="
            --cols-default: repeat(1, minmax(0, 1fr));
            --cols-lg: repeat(2, minmax(0, 1fr));
        "
    >

        <div class="col-span-1 min-w-0">
            @livewire(\App\Filament\Underwritten\Widgets\UnderwrittenBusinessAnual::class, [
                'reinsurer' => $this->reinsurer
            ], key('business-chart-' . $this->reinsurer))
        </div>
        <div class="col-span-1 min-w-0">
            @livewire(\App\Filament\Underwritten\Widgets\PremiumForPeriod::class, [
                'reinsurer' => $this->reinsurer
            ], key('premium-chart-' . $this->reinsurer))
        </div>
</div>
    
</x-filament::section>
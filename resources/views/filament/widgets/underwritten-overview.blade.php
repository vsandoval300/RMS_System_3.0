<x-filament::section heading="Underwritten Overview">

<div class="max-w-2xl">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 gap-6 mt-6">
        @livewire(\App\Filament\Underwritten\Widgets\UnderwrittenBusinessAnual::class, [
            'reinsurer' => $this->reinsurer
        ], key('business-chart-' . $this->reinsurer))

        @livewire(\App\Filament\Underwritten\Widgets\PremiumForPeriod::class, [
            'reinsurer' => $this->reinsurer
        ], key('premium-chart-' . $this->reinsurer))
    </div>
    
</x-filament::section>
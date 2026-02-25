<x-filament::section heading="Underwritten Overview">

<div class="max-w-xs">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        @livewire(\App\Filament\Widgets\UnderwrittenBusiness::class, [
            'reinsurer' => $this->reinsurer
        ], key('business-chart-' . $this->reinsurer))

        @livewire(\App\Filament\Underwritten\Resources\NoResource\Widgets\PremiumForPeriod::class, [
            'reinsurer' => $this->reinsurer
        ], key('premium-chart-' . $this->reinsurer))
    </div>
    
</x-filament::section>
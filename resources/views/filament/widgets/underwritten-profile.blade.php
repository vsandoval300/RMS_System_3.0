<x-filament::section heading="Underwritten Profile">

    <div class="max-w-2xl">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        @livewire(
           App\Filament\Underwritten\Widgets\UnderwrittenBusiness::class,
            [
                'reinsurer' => $this->reinsurer,
                'year' => $this->year
            ], key('businessYear-chart-' . $this->reinsurer . '-' . $this->year)
        )

        @livewire(
            App\Filament\Underwritten\Widgets\UnderwrittenPremium::class,
            [
                'reinsurer' => $this->reinsurer,
                'year' => $this->year
            ], key('premium-chart-' . $this->reinsurer . '-' . $this->year)
        )
    </div>

</x-filament::section>
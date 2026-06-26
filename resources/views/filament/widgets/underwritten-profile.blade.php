<x-filament::section heading="Underwritten Profile">

    <div class="max-w-xs">
        {{ $this->form }}
    </div>

    @php
        $widgets = [
            App\Filament\Underwritten\Widgets\UnderwrittenBusiness::make([
                'reinsurer' => $this->reinsurer,
                'years' => $this->years,
            ]),

            App\Filament\Underwritten\Widgets\UnderwrittenPremium::make([
                'reinsurer' => $this->reinsurer,
                'years' => $this->years,
            ]),
        ];
    @endphp

    <div class="mt-6">
        <x-filament-widgets::widgets
            :columns="2"
            :widgets="$widgets"
        />
    </div>

</x-filament::section>
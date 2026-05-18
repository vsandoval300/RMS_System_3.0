<x-filament::section heading="Underwritten Overview">

<div class="max-w-2xl">
        {{ $this->form }}
</div>

    @php
        $widgets = [    
            \App\Filament\Underwritten\Widgets\UnderwrittenBusinessAnual::make([
                'reinsurer' => $this->reinsurer
            ]),

            \App\Filament\Underwritten\Widgets\PremiumForPeriod::make([
                'reinsurer' => $this->reinsurer
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
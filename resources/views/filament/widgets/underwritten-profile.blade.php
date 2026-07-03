<x-filament::section heading="Underwritten Profile">

    @if (!$this->hideFilters)
    <div class="max-w-xs">
        {{ $this->form }}
    </div>
    @endif

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

    <style>
        .profile-charts-wrap .fi-section {
            background-color: #ffffff !important;
        }
        .dark .profile-charts-wrap .fi-section {
            background-color: #1e2533 !important;
        }
    </style>
    <div class="mt-6 profile-charts-wrap">
        <x-filament-widgets::widgets
            :columns="2"
            :widgets="$widgets"
        />
    </div>

</x-filament::section>
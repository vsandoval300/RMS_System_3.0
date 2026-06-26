<x-filament::section>

    {{-- ── Filters ────────────────────────────────────────────────── --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    @php
        $props = [
            'reinsurerId' => $this->reinsurerId,
            'dateFrom'    => $this->dateFrom,
            'dateTo'      => $this->dateTo,
        ];

        $row1 = [
            \App\Filament\Resources\Transactions\Widgets\TransactionStatsCards::make($props),
        ];

        $row2 = [
            \App\Filament\Resources\Transactions\Widgets\TransactionsByMonthChart::make($props),
            \App\Filament\Resources\Transactions\Widgets\TransactionsByStatusChart::make($props),
        ];

        $row3 = [
            \App\Filament\Resources\Transactions\Widgets\CompletionTrendChart::make($props),
            \App\Filament\Resources\Transactions\Widgets\OverdueTransactionsWidget::make($props),
        ];
    @endphp

    <x-filament-widgets::widgets :columns="1" :widgets="$row1" />
    <x-filament-widgets::widgets :columns="2" :widgets="$row2" />
    <x-filament-widgets::widgets :columns="2" :widgets="$row3" />

</x-filament::section>

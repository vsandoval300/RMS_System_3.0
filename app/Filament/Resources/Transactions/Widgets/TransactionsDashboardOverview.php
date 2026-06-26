<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Reinsurer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;

class TransactionsDashboardOverview extends Widget implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.widgets.transactions-dashboard-overview';
    protected int|string|array $columnSpan = 'full';

    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns(3)
                ->schema([
                    Select::make('reinsurerId')
                        ->label('Reinsurer')
                        ->placeholder('All Reinsurers')
                        ->options(Reinsurer::orderBy('short_name')->pluck('short_name', 'id'))
                        ->searchable()
                        ->live(),

                    DatePicker::make('dateFrom')
                        ->label('From date')
                        ->live(),

                    DatePicker::make('dateTo')
                        ->label('To date')
                        ->live(),
                ]),
        ];
    }
}

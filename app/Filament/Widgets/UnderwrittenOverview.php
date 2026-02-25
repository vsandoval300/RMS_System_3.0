<?php

namespace App\Filament\Widgets;

use App\Models\Reinsurer;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;

class UnderwrittenOverview extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.underwritten-overview';
    protected int|string|array $columnSpan = 'full';

    public ?int $reinsurer = null;

    protected function getFormSchema(): array
    {
        return [
            Select::make('reinsurer')
                ->label('Reinsurers')
                ->options(Reinsurer::pluck('name', 'id'))
                ->searchable()
                ->live(),
        ];
    }
}

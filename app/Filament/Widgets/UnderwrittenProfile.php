<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Widget;

use function Illuminate\Support\years;

class UnderwrittenProfile extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.underwritten-profile';
    protected int|string|array $columnSpan = 'full';

    public ?int $reinsurer = null;
    public ?int $year = null;

    public function mount():void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Select::make('reinsurer')
                    ->label('Reinsurer')
                    ->options(Reinsurer::pluck('name', 'id'))
                    ->searchable()
                    ->live(),

                Select::make('year')
                    ->label('Underwritten Year')
                    ->options(
                        range(date('Y'), 2018) // ajusta tu rango real
                    )
                    ->live(),
            ]),
        ];   
    }

    public function getYearOptions(): array
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear, 2010);
        
        return array_combine($years, $years);
    }
}

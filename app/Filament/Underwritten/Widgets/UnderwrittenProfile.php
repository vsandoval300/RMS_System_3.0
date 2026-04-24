<?php

namespace App\Filament\Underwritten\Widgets;

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
    public array $years = [];

    protected $listeners = ['refreshChart' => '$refresh'];

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Select::make('reinsurer')
                    ->label('Reinsurer')
                    ->options(Reinsurer::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->live(),

                Select::make('years')
                    ->label('Underwritten Year')
                    ->multiple()
                    ->options(
                       $this->getYearOptions() // ajusta tu rango real
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

    public function updatedReinsurer()
    {
        $this->dispatch('refreshChart');
    }

    public function updatedYears()
    {
        $this->dispatch('refreshChart');
    }
}

<?php

namespace App\Filament\Underwritten\Widgets;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Components\Grid;
use App\Models\Business;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Widget;

use Livewire\Attributes\On;

class UnderwrittenProfile extends Widget implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.widgets.underwritten-profile';
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';


    public ?int  $reinsurer    = null;
    public array $years        = [];
    public bool  $hideFilters  = false;

    protected $listeners = ['refreshChart' => '$refresh'];

    #[On('analytics-filters-updated')]
    public function updateFromAnalyticsFilters(int $year, ?int $reinsurer): void
    {
        $this->reinsurer = $reinsurer;
        $this->years     = [$year];
        $this->dispatch('refreshChart');
    }

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

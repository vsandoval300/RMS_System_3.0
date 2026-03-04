<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\ChartWidget;

class UnderwrittenPremium extends ChartWidget
{
    protected static ?string $heading = 'Underwritten Premium';

    public ?array $reinsurer = [];
    public ?int $year = null;

    protected static bool $isLazy = false;

       protected function getType(): string
    {
        return 'line';
    }

    protected function getListeners(): array
    {
        return [
            'refreshChart' => '$refresh',
        ];
    }

    protected function getData(): array
    {
        $service = app(PremiumForPeriodService::class);
        $year = $this->year ?? now()->year;

        // Si no se seleccionó reinsurers → Top 5 del año
        if (empty($this->reinsurer)) {
            $top = $service->topReinsurersByYear($year);
            $reinsurerIds = $top->pluck('id')->toArray();
        } else {
            $reinsurerIds = $this->reinsurer;
        }

        // Consulta mensual con FTP y FTS
        $results = $service->mensualFtpFtsPorReinsurers($reinsurerIds, $year);

        $months = collect([
            'Jan','Feb','Mar','Apr','May','Jun',
            'Jul','Aug','Sep','Oct','Nov','Dec'
        ]);

        $datasets = [];

        foreach ($results->groupBy('reinsurer_name') as $reinsurer => $data) {

            $valuesFtp = [];
            $valuesFts = [];

            foreach ($months as $month) {
                $record = $data->firstWhere('month_label', $month);
                $valuesFtp[] = $record ? (float) $record->ftp : 0;
                $valuesFts[] = $record ? (float) $record->fts : 0;
            }

            // // Línea sólida FTP
            // $datasets[] = [
            //     'label' => $reinsurer . ' (FTP)',
            //     'data' => $valuesFtp,
            //     'borderColor' => $this->colorFromString($reinsurer . 'ftp'),
            //     'backgroundColor' => 'transparent',
            //     'tension' => 0.3,
            // ];

            // Línea punteada FTS
            $datasets[] = [
                'label' => $reinsurer . ' (FTS)',
                'data' => $valuesFts,
                'borderColor' => $this->colorFromString($reinsurer . 'fts'),
                'backgroundColor' => 'transparent',
                //'borderDash' => [5,5],
                'tension' => 0.3,
            ];
        }

        return [
            'labels' => $months,
            'datasets' => $datasets,
        ];
    }

    // Genera un color consistente por string
    private function colorFromString(string $string): string
    {
        return '#' . substr(md5($string), 0, 6);
    }

}
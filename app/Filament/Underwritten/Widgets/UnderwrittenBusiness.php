<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UnderwrittenBusiness extends ChartWidget
{
    protected static ?string $heading = 'Businesses';

    public ?array $reinsurer = [];
    public ?int $year = null;

    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = $this->year ?? now()->year;

        $service = app(PremiumForPeriodService::class);

        // 🔥 TOP 5 automático si no hay selección
        if (empty($this->reinsurer)) {
            $top = $service->topReinsurersByYear($year);
            $reinsurerIds = $top->pluck('id')->toArray();
        } else {
            $reinsurerIds = $this->reinsurer;
        }

        if (empty($reinsurerIds)) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // 🔥 Consulta SQL optimizada PostgreSQL
        $rows = DB::table('businesses as b')
            ->join('reinsurers as r', 'r.id', '=', 'b.reinsurer_id')
            ->whereIn('r.id', $reinsurerIds)
            ->whereRaw('EXTRACT(YEAR FROM b.created_at) = ?', [$year])
            ->selectRaw("
                r.id as reinsurer_id,
                r.name as reinsurer_name,
                DATE_TRUNC('month', b.created_at) as month_date,
                TO_CHAR(DATE_TRUNC('month', b.created_at), 'Mon') as month_label,
                COUNT(*) as total
            ")
            ->groupBy('r.id', 'r.name', DB::raw("DATE_TRUNC('month', b.created_at)"))
            ->orderBy(DB::raw("DATE_TRUNC('month', b.created_at)"))
            ->get();

        $months = collect([
            'Jan','Feb','Mar','Apr','May','Jun',
            'Jul','Aug','Sep','Oct','Nov','Dec'
        ]);

        $datasets = [];

        foreach ($rows->groupBy('reinsurer_name') as $reinsurer => $data) {

            $values = [];

            foreach ($months as $month) {
                $record = $data->firstWhere('month_label', $month);
                $values[] = $record ? (int) $record->total : 0;
            }

            $datasets[] = [
                'label' => $reinsurer,
                'data' => $values,
                'borderColor' => $this->colorFromString($reinsurer),
                'backgroundColor' => 'transparent',
                'tension' => 0.3,
            ];
        }

        return [
            'labels' => $months->toArray(),
            'datasets' => $datasets,
        ];
    }

    // 🎨 Color consistente por reinsurer
    private function colorFromString(string $string): string
    {
        return '#' . substr(md5($string), 0, 6);
    }
}

// class UnderwrittenBusiness extends ChartWidget
// {
//     protected static ?string $heading = 'Chart';

//     public ?array $reinsurer = null;
//     public ?int $year = null;

//     protected static bool $isLazy = false;

//     protected function getData(): array
//     {
//         $query = Business::query();

//         if($this->reinsurer) {
//             $query->where('reinsurer_id', $this->reinsurer);
//         }

//         if ($this->year) {
//             $query->whereYear('created_at', $this->year);
//         }
//         $rows = $query
//             ->selectRaw("DATE_PART('year', created_at) AS year, COUNT(*) AS total")
//             ->groupBy('year')
//             ->orderBy('year')
//             ->get();

//         return [
//             'datasets' => [
//                 [
//                     'label' => 'Businesses',
//                     'data'  => $rows->pluck('total'),
//                 ],
//             ],
//             'labels' => $rows->pluck('year'),
//         ];

//     }

//     protected function getType(): string
//     {
//         return 'line';
//     }
// }

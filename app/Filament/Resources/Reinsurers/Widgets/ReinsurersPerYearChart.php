<?php

namespace App\Filament\Resources\Reinsurers\Widgets;

use App\Models\OperativeStatus;
use App\Models\Reinsurer;
use Filament\Widgets\ChartWidget;

class ReinsurersPerYearChart extends ChartWidget
{
    protected ?string $heading = 'Reinsurers by Year Established';
    protected ?string $maxHeight = '320px';
    protected int|string|array $columnSpan = 'full';

    private array $statusColors = [
        'OP' => ['rgba(144,190,109,0.85)', '#90BE6D'],  // Willow Green  – Operative
        'DV' => ['rgba(249,65,68,0.85)',   '#F94144'],  // Strawberry Red – Dissolved
        'RO' => ['rgba(243,114,44,0.85)',  '#F3722C'],  // Pumpkin Spice  – Run-off
        'TR' => ['rgba(39,125,161,0.85)',  '#277DA1'],  // Cerulean       – Transferred
        'PL' => ['rgba(249,199,79,0.85)',  '#F9C74F'],  // Tuscan Sun     – Pending License
        'DM' => ['rgba(87,117,144,0.85)',  '#577590'],  // Blue Slate     – Dormant
        'PI' => ['rgba(67,170,139,0.85)',  '#43AA8B'],  // Seaweed        – Pending Incorp
    ];

    protected function getData(): array
    {
        $years = Reinsurer::query()
            ->whereNotNull('established')
            ->whereNull('deleted_at')
            ->distinct()
            ->orderBy('established')
            ->pluck('established');

        $statuses = OperativeStatus::whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $counts = Reinsurer::query()
            ->whereNotNull('established')
            ->whereNull('deleted_at')
            ->selectRaw('established, operative_status_id, COUNT(*) as total')
            ->groupBy('established', 'operative_status_id')
            ->get()
            ->groupBy('established');

        $datasets = [];
        foreach ($statuses as $index => $status) {
            $data = [];
            foreach ($years as $year) {
                $row = ($counts->get($year) ?? collect())
                    ->firstWhere('operative_status_id', $status->id);
                $data[] = $row ? (int) $row->total : 0;
            }

            $color = $this->statusColors[$status->acronym]
                ?? ['rgba(65,162,195,0.85)', 'rgba(65,162,195,1)'];

            $dataset = [
                'label'           => $status->acronym . ' - ' . $status->description,
                'data'            => $data,
                'backgroundColor' => $color[0],
                'borderColor'     => $color[1],
                'borderWidth'     => 1,
                'borderRadius'    => 0,
            ];

            if ($index === 0) {
                $dataset['chartId'] = 'reinsurers-per-year';
            }

            $datasets[] = $dataset;
        }

        return [
            'labels'   => $years->map(fn ($y) => (string) $y)->all(),
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'layout' => [
                'padding' => ['top' => 24],
            ],
            'scales' => [
                'y' => [
                    'stacked'     => true,
                    'beginAtZero' => true,
                    'ticks'       => [
                        'stepSize' => 1,
                        'color'    => 'rgba(255,255,255,0.75)',
                        'font'     => ['size' => 12],
                    ],
                    'grid'        => ['color' => 'rgba(156,163,175,0.15)'],
                ],
                'x' => [
                    'stacked' => true,
                    'ticks'   => [
                        'color' => 'rgba(255,255,255,0.75)',
                        'font'  => ['size' => 12],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'bottom',
                    'labels'   => [
                        'color'     => 'rgba(255,255,255,0.75)',
                        'font'      => ['size' => 13],
                        'boxWidth'  => 12,
                        'boxHeight' => 12,
                        'padding'   => 16,
                    ],
                ],
            ],
        ];
    }
}

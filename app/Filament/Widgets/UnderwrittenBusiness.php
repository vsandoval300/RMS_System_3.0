<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class UnderwrittenBusiness extends ChartWidget
{
    protected static ?string $heading = 'Underwritten Trend (demo)';
    protected static ?string $maxHeight = '320px';
    protected static bool $isLazy = false;          // para ver el demo al instante
    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * DEMO: datos estáticos (sin BD)
     */
    protected function getData(): array
    {
        // Etiquetas de ejemplo (12 meses)
        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
        ];

        // Serie de ejemplo (números arbitrarios)
        $series = [5, 9, 7, 11, 8, 13, 10, 12, 9, 15, 14, 18];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Businesses (demo)',
                    'data' => $series,
                    'tension' => 0.35,     // suaviza la línea
                    'pointRadius' => 3,
                    'borderWidth' => 2,
                    'fill' => false,
                ],
            ],
        ];
    }

    /**
     * Opcional: configuración de Chart.js
     */
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => ['mode' => 'index', 'intersect' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['display' => true],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PortfolioGrowthByReinsurerExport implements
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
{
    protected array $years;
    protected array $rows;
    protected array $totals;
    protected float $grandTotal;

    public function __construct(array $data)
    {
        $this->years      = $data['years'];
        $this->rows       = $data['rows'];
        $this->totals     = $data['totals'];
        $this->grandTotal = $data['grand_total'];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Reinsurer',
            ...array_map(fn ($year) => (string) $year, $this->years),
            'Total',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->rows as $row) {
            $rows[] = [
                $row['id'],
                $row['name'],
                ...array_map(fn ($year) => round($row['amounts'][$year], 0), $this->years),
                round($row['total'], 0),
            ];
        }

        $rows[] = [
            '',
            'Total',
            ...array_map(fn ($year) => round($this->totals[$year], 0), $this->years),
            round($this->grandTotal, 0),
        ];

        return $rows;
    }

    public function columnFormats(): array
    {
        $formats = [];

        // Year columns start at C, plus the trailing Total column.
        $lastColumnIndex = 2 + count($this->years) + 1;

        for ($i = 3; $i <= $lastColumnIndex; $i++) {
            $formats[Coordinate::stringFromColumnIndex($i)] = '#,##0';
        }

        return $formats;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->rows) + 2; // +1 for header row, +1 for totals row

        return [
            1 => [
                'font' => ['bold' => true],
            ],
            $lastRow => [
                'font' => ['bold' => true],
            ],
        ];
    }
}

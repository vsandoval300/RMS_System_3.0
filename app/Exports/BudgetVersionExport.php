<?php

namespace App\Exports;

use App\Models\UnderwrittenBudget;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BudgetVersionExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private const MONTHS = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
    private const MONTH_NAMES = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    private int $dataRowCount = 0;

    public function __construct(private readonly UnderwrittenBudget $budget) {}

    public function title(): string
    {
        return "Budget {$this->budget->year} v{$this->budget->version}";
    }

    public function headings(): array
    {
        $monthHeaders = [];
        foreach (self::MONTH_NAMES as $i => $name) {
            $monthHeaders[] = sprintf('%04d%02d', $this->budget->year, $i + 1) . "\n" . $name;
        }

        return array_merge(['#', 'Reinsurer'], $monthHeaders, ['Total (USD)', '% of Total']);
    }

    public function array(): array
    {
        $items      = $this->budget->items->sortBy('reinsurer_id');
        $grandTotal = $items->sum('premium_budget');
        $rows       = [];

        foreach ($items as $item) {
            $rowTotal   = $item->month_total;
            $pct        = $grandTotal > 0 ? round(($rowTotal / $grandTotal) * 100, 2) : 0;
            $row        = [$item->reinsurer_id, $item->reinsurer?->name ?? '—'];

            foreach (self::MONTHS as $mk) {
                $row[] = (float) $item->$mk ?: null;
            }

            $row[] = round($rowTotal, 2);
            $row[] = $pct / 100;   // formatted as percentage by style
            $rows[] = $row;
        }

        $this->dataRowCount = count($rows);

        // Totals row
        $totalRow = ['', 'TOTAL'];
        foreach (self::MONTHS as $mk) {
            $totalRow[] = round((float) $items->sum(fn($i) => (float) $i->$mk), 2) ?: null;
        }
        $totalRow[] = round($grandTotal, 2);
        $totalRow[] = null;
        $rows[] = $totalRow;

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol   = 'P';   // # + Reinsurer + 12 months + Total + Pct = 16 cols → A..P
        $lastData  = $this->dataRowCount + 1;  // +1 for heading row
        $totalRow  = $lastData + 1;

        // Heading row
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);

        // Data rows
        $sheet->getStyle("A2:{$lastCol}{$lastData}")->applyFromArray([
            'font'      => ['size' => 8.5],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // Month columns: right-aligned, tabular nums
        $sheet->getStyle("C2:N{$lastData}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
        ]);

        // Total column
        $sheet->getStyle("O2:O{$totalRow}")->applyFromArray([
            'font'         => ['bold' => true, 'size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eef2ff']],
        ]);

        // % column
        $sheet->getStyle("P2:P{$lastData}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0.0%'],
        ]);

        // Totals row
        $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '93c5fd']]],
            'numberFormat' => ['formatCode' => '#,##0.00'],
        ]);
        $sheet->getStyle("B{$totalRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Reinsurer column: left-align
        $sheet->getStyle("B2:B{$totalRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(32);
        foreach (range('C', 'N') as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }
        $sheet->getColumnDimension('O')->setWidth(14);
        $sheet->getColumnDimension('P')->setWidth(10);

        // Freeze panes: keep # + Reinsurer visible while scrolling months
        $sheet->freezePane('C2');

        return [];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BudgetTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private const MONTHS      = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
    private const MONTH_NAMES = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

    private int $rowCount = 0;

    /**
     * @param int    $year
     * @param int    $nextVersion
     * @param array  $rows  [reinsurer_id => ['name'=>string, 'cns_code'=>string, 'm01'...'m12'=>string]]
     */
    public function __construct(
        private readonly int   $year,
        private readonly int   $nextVersion,
        private readonly array $rows
    ) {}

    public function title(): string
    {
        return "Budget {$this->year} Template";
    }

    public function headings(): array
    {
        return array_merge(
            ['ID', 'CNS', 'Reinsurer', 'INC'],
            self::MONTH_NAMES
        );
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->rows as $id => $row) {
            $dataRow = [
                (int) $id,
                $row['cns_code'] ?? '',
                $row['name'],
                ($row['included'] ?? true) ? 1 : null,
            ];
            foreach (self::MONTHS as $mk) {
                $val = (float) str_replace(',', '', $row[$mk] ?? '0');
                $dataRow[] = $val > 0 ? $val : null;
            }
            $data[] = $dataRow;
        }

        $this->rowCount = count($data);
        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $this->rowCount + 1; // +1 header

        // ── Header row ────────────────────────────────────────
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                             'color'       => ['rgb' => '374151']]],
        ]);

        // ── ID + CNS: read-only feel (gray bg) ───────────────
        $sheet->getStyle("A2:B{$lastRow}")->applyFromArray([
            'font'      => ['size' => 8.5, 'color' => ['rgb' => '9ca3af']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f3f4f6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR,
                                             'color'       => ['rgb' => 'e5e7eb']]],
        ]);

        // ── Reinsurer name: read-only feel ────────────────────
        $sheet->getStyle("C2:C{$lastRow}")->applyFromArray([
            'font'      => ['size' => 8.5, 'bold' => true, 'color' => ['rgb' => '374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR,
                                             'color'       => ['rgb' => 'e5e7eb']]],
        ]);

        // ── INC column: editable, centered, yellow tint ──────
        $sheet->getStyle("D1:D{$lastRow}")->applyFromArray([
            'font'         => ['size' => 8.5, 'bold' => true, 'color' => ['rgb' => '374151']],
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                               'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                'color'       => ['rgb' => 'fde68a']]],
            'numberFormat' => ['formatCode' => '0'],
        ]);

        // ── Month columns: editable (white bg, bordered) ─────
        $sheet->getStyle("E2:P{$lastRow}")->applyFromArray([
            'font'         => ['size' => 8.5],
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT,
                               'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                'color'       => ['rgb' => 'c7d2e6']]],
            'numberFormat' => ['formatCode' => '#,##0.00'],
        ]);

        // ── Column widths ─────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(7);
        $sheet->getColumnDimension('C')->setWidth(32);
        $sheet->getColumnDimension('D')->setWidth(5);   // INC
        foreach (range('E', 'P') as $col) {
            $sheet->getColumnDimension($col)->setWidth(13);
        }

        // ── Freeze: keep ID + CNS + Name + INC visible on scroll
        $sheet->freezePane('E2');

        // ── Instruction comment on cell A1 ────────────────────
        $sheet->getComment('A1')->getText()->createTextRun(
            "BUDGET IMPORT TEMPLATE — Year: {$this->year} | Will create: v{$this->nextVersion}\n" .
            "DO NOT modify columns A (ID), B (CNS) or C (Reinsurer).\n" .
            "Column D (INC): 1 = include row, 0 = exclude row.\n" .
            "Enter monthly amounts in columns E–P (JAN–DEC).\n" .
            "Save as .xlsx and upload in the system."
        );

        return [];
    }
}

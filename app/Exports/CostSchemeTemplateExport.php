<?php

namespace App\Exports;

use App\Models\Deduction;
use App\Models\Partner;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CostSchemeTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $deductions,
        private readonly array $partners,
    ) {}

    public function sheets(): array
    {
        return [
            new CostSchemesDataSheet(),
            new CostNodexDataSheet(),
            new CsRefDeductionsSheet($this->deductions),
            new CsRefPartnersSheet($this->partners),
            new CsReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            deductions: Deduction::orderBy('concept')->get(['id', 'concept', 'description'])->toArray(),
            partners:   Partner::orderBy('name')->get(['id', 'name'])->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — CostSchemes (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class CostSchemesDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 50;

    public function title(): string { return 'CostSchemes'; }

    public function headings(): array
    {
        return [
            'scheme_id',       // A  required / PK (string ≤ 19)
            'index',           // B  integer, required
            'share',           // C  float (e.g. 30.5 = 30.5%), required
            'agreement_type',  // D  enum, required
            'description',     // E  optional
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 5, null));
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 10, 'C' => 14, 'D' => 20, 'E' => 50];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = self::EMPTY_ROWS + 1;

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);

        // A — scheme_id: yellow tint = PK
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // B — index: white numeric
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // C — share: numeric (percentage value)
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0.00'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // D — agreement_type: green tint (enum)
        $sheet->getStyle("D2:D{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
        ]);

        // E — description: plain white
        $sheet->getStyle("E2:E{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'      => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment' => ['wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "COST SCHEMES SHEET\n" .
            "──────────────────────────────\n" .
            "A  scheme_id       REQUIRED · PK · max 19 chars · upsert key\n" .
            "B  index           REQUIRED · integer\n" .
            "C  share           REQUIRED · numeric (e.g. 30.5 for 30.5%)\n" .
            "D  agreement_type  REQUIRED · Quota Share | Surplus | Excess of Loss | Stop Loss\n" .
            "E  description     optional · free text\n" .
            "\nExisting scheme_id → UPDATE.\nNew scheme_id → INSERT."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — CostNodesx (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class CostNodexDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'CostNodesx'; }

    public function headings(): array
    {
        return [
            'scheme_id',           // A  FK → CostSchemes.scheme_id, required
            'index',               // B  integer, required (upsert key with scheme_id)
            'deduction_id',        // C  FK → REF_Deductions.id, required
            'value',               // D  float, required
            'apply_to_gross',      // E  Yes / No, optional (default: No)
            'partner_source',      // F  name → REF_Partners, required
            'partner_destination', // G  name → REF_Partners, required
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 7, null));
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 10, 'C' => 16, 'D' => 14, 'E' => 16, 'F' => 32, 'G' => 32];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = self::EMPTY_ROWS + 1;

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);

        // A — scheme_id: blue tint (FK to Sheet 1)
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // B — index: white numeric
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // C — deduction_id: blue tint (FK to REF_Deductions)
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // D — value: numeric
        $sheet->getStyle("D2:D{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0.0000'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // E — apply_to_gross: green tint (enum Yes/No)
        $sheet->getStyle("E2:E{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
        ]);

        // F–G — partners: blue tint (FK to REF_Partners)
        $sheet->getStyle("F2:G{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "COST NODESX SHEET\n" .
            "──────────────────────────────\n" .
            "A  scheme_id           REQUIRED · FK to CostSchemes sheet (or existing scheme)\n" .
            "B  index               REQUIRED · integer · upsert key with scheme_id\n" .
            "C  deduction_id        REQUIRED · numeric ID from REF_Deductions sheet\n" .
            "D  value               REQUIRED · numeric (e.g. 10.5)\n" .
            "E  apply_to_gross      optional · Yes or No · default: No\n" .
            "F  partner_source      REQUIRED · name from REF_Partners sheet\n" .
            "G  partner_destination REQUIRED · name from REF_Partners sheet\n" .
            "\nNode matched by scheme_id + index.\nExisting pair → UPDATE. New pair → INSERT."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 3 — REF: Deductions
// ─────────────────────────────────────────────────────────────────────────────

class CsRefDeductionsSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Deductions'; }
    public function headings(): array { return ['ID (use in column C of CostNodesx)', 'Concept', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['id'], $r['concept'], $r['description'] ?? ''], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = max(count($this->rows) + 1, 2);
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(55);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 4 — REF: Partners
// ─────────────────────────────────────────────────────────────────────────────

class CsRefPartnersSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Partners'; }
    public function headings(): array { return ['ID', 'Name (use in columns F and G of CostNodesx)']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['id'], $r['name']], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = max(count($this->rows) + 1, 2);
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(45);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 5 — README
// ─────────────────────────────────────────────────────────────────────────────

class CsReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['COST SCHEMES IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on either data sheet.'],
            ['• scheme_id (CostSchemes sheet) is the upsert key. Existing id → UPDATE, new id → INSERT.'],
            ['• The scheme_id used in the CostNodesx sheet must match a scheme_id in the CostSchemes sheet (this file) OR an existing scheme already in the system.'],
            ['• Nodes are matched by scheme_id + index. Existing pair → UPDATE. New pair → INSERT.'],
            ['• All rows on both sheets are validated before any data is saved. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COSTSCHEMES SHEET COLUMNS'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'scheme_id',      'YES', 'String up to 19 chars. PK / upsert key. Example: CS-2026-001'],
            ['B', 'index',          'YES', 'Integer. Ordering index.'],
            ['C', 'share',          'YES', 'Numeric percentage (e.g. 30.5 for 30.5%).'],
            ['D', 'agreement_type', 'YES', 'Quota Share   |   Surplus   |   Excess of Loss   |   Stop Loss'],
            ['E', 'description',    'NO',  'Free text description of the scheme.'],
            [''],
            ['COSTNODESX SHEET COLUMNS'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'scheme_id',           'YES', 'Must match a scheme_id in the CostSchemes sheet or an existing scheme in the system.'],
            ['B', 'index',               'YES', 'Integer. Used as upsert key together with scheme_id.'],
            ['C', 'deduction_id',        'YES', 'Numeric ID from the REF_Deductions sheet (column A).'],
            ['D', 'value',               'YES', 'Numeric value (e.g. 10.5).'],
            ['E', 'apply_to_gross',      'NO',  'Yes or No. Defaults to No if empty.'],
            ['F', 'partner_source',      'YES', 'Must match exactly a Name in the REF_Partners sheet.'],
            ['G', 'partner_destination', 'YES', 'Must match exactly a Name in the REF_Partners sheet.'],
            [''],
            ['COLOR CODING (data sheets)'],
            ['• Yellow background  = Primary key column (scheme_id). This is the upsert key.'],
            ['• Blue background    = Foreign key / lookup column. Use the reference sheets.'],
            ['• Green background   = Enum column. Only listed values are accepted.'],
            ['• White background   = Free text or numeric input.'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
        ]);
        foreach ([3, 11, 19, 27] as $row) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            ]);
        }
        foreach ([12, 20] as $headerRow) {
            $sheet->getStyle("A{$headerRow}:D{$headerRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            ]);
        }
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(75);
        return [];
    }
}

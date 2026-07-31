<?php

namespace App\Exports;

use App\Models\Business;
use App\Models\Coverage;
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

class LiabilityStructureTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $businesses,
        private readonly array $coverages,
    ) {}

    public function sheets(): array
    {
        return [
            new LiabilityStructuresDataSheet(),
            new LsRefBusinessesSheet($this->businesses),
            new LsRefCoveragesSheet($this->coverages),
            new LsReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            businesses: Business::orderBy('business_code')
                ->get(['business_code', 'description'])
                ->toArray(),
            coverages:  Coverage::orderBy('name')
                ->get(['id', 'name', 'acronym', 'description'])
                ->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — LiabilityStructures (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class LiabilityStructuresDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'LiabilityStructures'; }

    public function headings(): array
    {
        return [
            'business_code',  // A  FK → businesses.business_code, required
            'coverage_name',  // B  FK → REF_Coverages.name, required
            'cls',            // C  Yes / No (default: No)
            'limit',          // D  float, required
            'limit_desc',     // E  text, required
            'sublimit',       // F  float, optional
            'sublimit_desc',  // G  text, optional
            'deductible',     // H  float, optional
            'deductible_desc',// I  text, optional
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 9, null));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 28, 'C' => 10,
            'D' => 16, 'E' => 40,
            'F' => 16, 'G' => 40,
            'H' => 16, 'I' => 40,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = self::EMPTY_ROWS + 1;

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);

        // A — business_code: yellow tint (FK key column)
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // B — coverage_name: blue tint (FK lookup)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // C — cls: green tint (enum Yes/No)
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'font'      => ['size' => 8.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
        ]);

        // D — limit: required numeric
        $sheet->getStyle("D2:D{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // E — limit_desc: required text
        $sheet->getStyle("E2:E{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'      => ['size' => 8.5],
            'alignment' => ['wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // F, H — optional numeric (sublimit, deductible): gray
        $sheet->getStyle("F2:F{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'         => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);
        $sheet->getStyle("H2:H{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'         => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // G, I — optional text (sublimit_desc, deductible_desc): gray
        $sheet->getStyle("G2:G{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'      => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment' => ['wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);
        $sheet->getStyle("I2:I{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'      => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment' => ['wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "LIABILITY STRUCTURES IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  business_code   REQUIRED · FK → businesses. See REF_Businesses sheet.\n" .
            "B  coverage_name   REQUIRED · FK → coverages. See REF_Coverages sheet.\n" .
            "C  cls             optional · Yes or No · default: No\n" .
            "D  limit           REQUIRED · numeric\n" .
            "E  limit_desc      REQUIRED · text\n" .
            "F  sublimit        optional · numeric\n" .
            "G  sublimit_desc   optional · text\n" .
            "H  deductible      optional · numeric\n" .
            "I  deductible_desc optional · text\n" .
            "\nNote: index is assigned automatically. Each row creates a new record."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Businesses
// ─────────────────────────────────────────────────────────────────────────────

class LsRefBusinessesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Businesses'; }
    public function headings(): array { return ['business_code (use in column A)', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['business_code'], mb_substr($r['description'] ?? '', 0, 120)], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = max(count($this->rows) + 1, 2);
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(60);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 3 — REF: Coverages
// ─────────────────────────────────────────────────────────────────────────────

class LsRefCoveragesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Coverages'; }
    public function headings(): array { return ['ID', 'Name (use in column B)', 'Acronym', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [
            $r['id'],
            $r['name'],
            $r['acronym'] ?? '',
            mb_substr($r['description'] ?? '', 0, 120),
        ], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = max(count($this->rows) + 1, 2);
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getStyle("D2:D{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(32);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(55);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 4 — README
// ─────────────────────────────────────────────────────────────────────────────

class LsReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['LIABILITY STRUCTURES IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the LiabilityStructures sheet.'],
            ['• Each row creates a new liability structure record. There is no update/upsert — duplicate rows will create duplicate records.'],
            ['• business_code must match an existing business in the system (see REF_Businesses sheet).'],
            ['• coverage_name must match exactly a Name in REF_Coverages sheet.'],
            ['• The index field is assigned automatically — do not include it.'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'business_code',   'YES', 'Must match an existing business_code. See REF_Businesses sheet.'],
            ['B', 'coverage_name',   'YES', 'Must match exactly a Name in REF_Coverages sheet.'],
            ['C', 'cls',             'NO',  'Yes or No. Defaults to No if empty. CLS = Combined Limit with Sublimit.'],
            ['D', 'limit',           'YES', 'Numeric value (e.g. 1000000.00).'],
            ['E', 'limit_desc',      'YES', 'Text description of the limit.'],
            ['F', 'sublimit',        'NO',  'Optional numeric sublimit value.'],
            ['G', 'sublimit_desc',   'NO',  'Optional text description of the sublimit.'],
            ['H', 'deductible',      'NO',  'Optional numeric deductible value.'],
            ['I', 'deductible_desc', 'NO',  'Optional text description of the deductible.'],
            [''],
            ['COLOR CODING'],
            ['• Yellow background = Key FK column (business_code). Must match an existing business.'],
            ['• Blue background   = Lookup column. Use the reference sheets to find valid values.'],
            ['• Green background  = Enum column. Only Yes or No accepted.'],
            ['• White background  = Required free text or numeric input.'],
            ['• Gray background   = Optional field. Leave empty if not applicable.'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
        ]);
        foreach ([3, 12] as $row) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            ]);
        }
        $sheet->getStyle('A13:D13')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
        ]);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(75);
        return [];
    }
}

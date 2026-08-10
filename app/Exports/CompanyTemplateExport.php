<?php

namespace App\Exports;

use App\Models\Country;
use App\Models\Industry;
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

class CompanyTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $countries,
        private readonly array $industries,
    ) {}

    public function sheets(): array
    {
        return [
            new CompaniesDataSheet(),
            new CoRefCountriesSheet($this->countries),
            new CoRefIndustriesSheet($this->industries),
            new CoReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            countries:  Country::orderBy('name')->get(['id', 'name'])->toArray(),
            industries: Industry::orderBy('name')->get(['id', 'name'])->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — Companies (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class CompaniesDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'Companies'; }

    public function headings(): array
    {
        return [
            'name',           // A  required
            'acronym',        // B  required, upsert key
            'activity',       // C  optional text
            'country_name',   // D  optional FK → REF_Countries
            'industry_name',  // E  optional FK → REF_Industries
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 5, null));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 36, 'B' => 16, 'C' => 36,
            'D' => 28, 'E' => 28,
        ];
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

        // A — name: required (white)
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // B — acronym: yellow tint (upsert key)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // C — optional text (activity): gray
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'    => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // D, E — FK lookup columns: blue tint
        foreach (['D', 'E'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$last}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
                'font'    => ['size' => 8.5],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
            ]);
        }

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "COMPANIES IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  name           REQUIRED · Company full name\n" .
            "B  acronym        REQUIRED · Upsert key — if an existing company has this acronym, it will be UPDATED; otherwise a new record is inserted.\n" .
            "C  activity       optional · Business activity description\n" .
            "D  country_name   optional · FK → countries. See REF_Countries sheet.\n" .
            "E  industry_name  optional · FK → industries. See REF_Industries sheet.\n" .
            "\nNote: Empty rows are skipped. Rows missing both name and acronym are ignored."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Countries
// ─────────────────────────────────────────────────────────────────────────────

class CoRefCountriesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Countries'; }
    public function headings(): array { return ['ID', 'Name (use in column D)']; }
    public function array(): array
    {
        return array_map(fn ($r) => [$r['id'], $r['name']], $this->rows);
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
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(40);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 3 — REF: Industries
// ─────────────────────────────────────────────────────────────────────────────

class CoRefIndustriesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Industries'; }
    public function headings(): array { return ['ID', 'Name (use in column E)']; }
    public function array(): array
    {
        return array_map(fn ($r) => [$r['id'], $r['name']], $this->rows);
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
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(40);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 4 — README
// ─────────────────────────────────────────────────────────────────────────────

class CoReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['COMPANIES IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the Companies sheet.'],
            ['• acronym is the UPSERT KEY. If a company with the same acronym already exists, it will be UPDATED. Otherwise, a new record is created.'],
            ['• name and acronym are required for every row.'],
            ['• country_name and industry_name must match exactly the Name in REF_Countries / REF_Industries sheets (case-insensitive).'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows (both name and acronym blank) are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Notes'],
            ['A', 'name',          'YES', 'Full company name.'],
            ['B', 'acronym',       'YES', 'Short identifier. Used as the upsert key — existing company with this acronym is updated.'],
            ['C', 'activity',      'NO',  'Business activity description.'],
            ['D', 'country_name',  'NO',  'Must match a Name in REF_Countries. Leave blank to leave country unset.'],
            ['E', 'industry_name', 'NO',  'Must match a Name in REF_Industries. Leave blank to leave industry unset.'],
            [''],
            ['COLOR CODING'],
            ['• White background   = Required field (name).'],
            ['• Yellow background  = Upsert key (acronym). Drives insert-or-update logic.'],
            ['• Blue background    = FK lookup column. Use the reference sheets to find valid values.'],
            ['• Gray background    = Optional field. Leave empty if not applicable.'],
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
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(75);
        return [];
    }
}

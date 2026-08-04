<?php

namespace App\Exports;

use App\Models\Country;
use App\Models\PartnerType;
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

class PartnerTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $partnerTypes,
        private readonly array $countries,
    ) {}

    public function sheets(): array
    {
        return [
            new PartnersDataSheet(),
            new PaRefPartnerTypesSheet($this->partnerTypes),
            new PaRefCountriesSheet($this->countries),
            new PaReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            partnerTypes: PartnerType::orderBy('name')->get(['id', 'name', 'acronym'])->toArray(),
            countries:    Country::orderBy('name')->get(['id', 'name'])->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — Partners (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class PartnersDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'Partners'; }

    public function headings(): array
    {
        return [
            'name',               // A  required, upsert key
            'short_name',         // B  optional
            'acronym',            // C  optional
            'partner_type_name',  // D  optional FK → REF_PartnerTypes
            'country_name',       // E  optional FK → REF_Countries
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 5, null));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40, 'B' => 28, 'C' => 14,
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

        // A — name: yellow tint (upsert key)
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // B, C — optional text (short_name, acronym): gray
        foreach (['B', 'C'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$last}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
                'font'    => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
            ]);
        }

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
            "PARTNERS IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  name               REQUIRED · Upsert key — if a partner with this name already exists, it will be UPDATED; otherwise a new record is inserted.\n" .
            "B  short_name         optional · Abbreviated name\n" .
            "C  acronym            optional · Short identifier (max 3 characters)\n" .
            "D  partner_type_name  optional · FK → partner_types. See REF_PartnerTypes sheet.\n" .
            "E  country_name       optional · FK → countries. See REF_Countries sheet.\n" .
            "\nNote: Empty rows are skipped."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Partner Types
// ─────────────────────────────────────────────────────────────────────────────

class PaRefPartnerTypesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_PartnerTypes'; }
    public function headings(): array { return ['ID', 'Name (use in column D)', 'Acronym']; }
    public function array(): array
    {
        return array_map(fn ($r) => [$r['id'], $r['name'], $r['acronym'] ?? ''], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = max(count($this->rows) + 1, 2);
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8.5, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(36);
        $sheet->getColumnDimension('C')->setWidth(14);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 3 — REF: Countries
// ─────────────────────────────────────────────────────────────────────────────

class PaRefCountriesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Countries'; }
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

class PaReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['PARTNERS IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the Partners sheet.'],
            ['• name is the UPSERT KEY. If a partner with the same name already exists, it will be UPDATED. Otherwise, a new record is created.'],
            ['• name is required for every row.'],
            ['• partner_type_name and country_name must match exactly the Name in REF_PartnerTypes / REF_Countries sheets (case-insensitive).'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows (name blank) are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Notes'],
            ['A', 'name',              'YES', 'Full partner name. Used as the upsert key — existing partner with this name is updated.'],
            ['B', 'short_name',        'NO',  'Abbreviated or display name.'],
            ['C', 'acronym',           'NO',  'Short identifier (e.g. initials). Maximum 3 characters.'],
            ['D', 'partner_type_name', 'NO',  'Must match a Name in REF_PartnerTypes. Leave blank to leave type unset.'],
            ['E', 'country_name',      'NO',  'Must match a Name in REF_Countries. Leave blank to leave country unset.'],
            [''],
            ['COLOR CODING'],
            ['• Yellow background  = Upsert key (name). Drives insert-or-update logic.'],
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
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(75);
        return [];
    }
}

<?php

namespace App\Exports;

use App\Models\Business;
use App\Models\BusinessDocType;
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

class OperativeDocTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $businesses,
        private readonly array $docTypes,
    ) {}

    public function sheets(): array
    {
        return [
            new OperativeDocsDataSheet(),
            new OdRefBusinessesSheet($this->businesses),
            new OdRefDocTypesSheet($this->docTypes),
            new OdReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            businesses: Business::orderBy('business_code')
                ->get(['business_code', 'description'])
                ->toArray(),
            docTypes: BusinessDocType::orderBy('name')
                ->get(['id', 'name', 'description'])
                ->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — OperativeDocs (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class OperativeDocsDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'OperativeDocs'; }

    public function headings(): array
    {
        return [
            'id',              // A  PK (string ≤19, upsert key), required
            'business_code',   // B  FK → businesses.business_code, required
            'doc_type_name',   // C  FK → business_doc_types.name, required
            'description',     // D  text, required
            'inception_date',  // E  date YYYY-MM-DD, required
            'expiration_date', // F  date YYYY-MM-DD, required
            'af_mf',           // G  float, required
            'roe_fs',          // H  float, optional
            'rep_date',        // I  date YYYY-MM-DD, optional
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 9, null));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 22, 'C' => 28,
            'D' => 40, 'E' => 18, 'F' => 18,
            'G' => 14, 'H' => 14, 'I' => 18,
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

        // A — id: yellow (PK / upsert key)
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // B — business_code: blue (FK)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // C — doc_type_name: blue (FK lookup)
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // D — description: required text (white)
        $sheet->getStyle("D2:D{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'      => ['size' => 8.5],
            'alignment' => ['wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // E, F — inception_date, expiration_date: required white + date format
        foreach (['E', 'F'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$last}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                'font'      => ['size' => 8.5],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
            ]);
            $sheet->getStyle("{$col}2:{$col}{$last}")
                ->getNumberFormat()
                ->setFormatCode('YYYY-MM-DD');
        }

        // G — af_mf: required float (white)
        $sheet->getStyle("G2:G{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.000000'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // H — roe_fs: optional float (gray)
        $sheet->getStyle("H2:H{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'         => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.000000'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // I — rep_date: optional date (gray)
        $sheet->getStyle("I2:I{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'      => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);
        $sheet->getStyle("I2:I{$last}")
            ->getNumberFormat()
            ->setFormatCode('YYYY-MM-DD');

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "OPERATIVE DOCUMENTS IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  id               REQUIRED · Document ID (max 19 chars). Upsert key: existing id → update, new id → insert.\n" .
            "B  business_code    REQUIRED · FK → businesses. See REF_Businesses sheet.\n" .
            "C  doc_type_name    REQUIRED · FK → business_doc_types.name. See REF_DocTypes sheet.\n" .
            "D  description      REQUIRED · Free text description.\n" .
            "E  inception_date   REQUIRED · Date (YYYY-MM-DD).\n" .
            "F  expiration_date  REQUIRED · Date (YYYY-MM-DD).\n" .
            "G  af_mf            REQUIRED · Adjustment Factor / Market Factor (float).\n" .
            "H  roe_fs           optional · Rate of Exchange (float).\n" .
            "I  rep_date         optional · Reporting date (YYYY-MM-DD).\n" .
            "\nNote: index is assigned automatically. document_path is not included in this import."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Businesses
// ─────────────────────────────────────────────────────────────────────────────

class OdRefBusinessesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Businesses'; }
    public function headings(): array { return ['business_code (use in column B)', 'Description']; }
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
// Sheet 3 — REF: Document Types
// ─────────────────────────────────────────────────────────────────────────────

class OdRefDocTypesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_DocTypes'; }
    public function headings(): array { return ['ID', 'Name (use in column C)', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [
            $r['id'],
            $r['name'],
            mb_substr($r['description'] ?? '', 0, 120),
        ], $this->rows);
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
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(60);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 4 — README
// ─────────────────────────────────────────────────────────────────────────────

class OdReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['OPERATIVE DOCUMENTS IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the OperativeDocs sheet.'],
            ['• Column A (id) is the upsert key: if the id already exists → the record is updated. If not → a new record is inserted.'],
            ['• business_code must match an existing business in the system (see REF_Businesses sheet).'],
            ['• doc_type_name must match exactly a Name in the REF_DocTypes sheet.'],
            ['• The index field is assigned automatically — do not include it.'],
            ['• document_path is not included in this import (managed separately through the UI).'],
            ['• Dates must be entered as YYYY-MM-DD (e.g. 2024-01-15). You may also type the date and let Excel format it.'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'id',               'YES', 'Document ID string (max 19 chars). Upsert key. Suggested format: {business_code}-{NN} e.g. BSNS-01.'],
            ['B', 'business_code',    'YES', 'Must match an existing business_code. See REF_Businesses sheet.'],
            ['C', 'doc_type_name',    'YES', 'Must match exactly a Name from REF_DocTypes sheet.'],
            ['D', 'description',      'YES', 'Free text description of the operative document.'],
            ['E', 'inception_date',   'YES', 'Start date in YYYY-MM-DD format.'],
            ['F', 'expiration_date',  'YES', 'End date in YYYY-MM-DD format.'],
            ['G', 'af_mf',            'YES', 'Adjustment Factor / Market Factor. Numeric (e.g. 1.000000).'],
            ['H', 'roe_fs',           'NO',  'Rate of Exchange (optional numeric). Leave empty if not applicable.'],
            ['I', 'rep_date',         'NO',  'Reporting date. Optional, YYYY-MM-DD format.'],
            [''],
            ['COLOR CODING'],
            ['• Yellow background = PK / Upsert key (id). Always required; drives insert vs. update logic.'],
            ['• Blue background   = FK lookup column. Use the reference sheets to find valid values.'],
            ['• White background  = Required free text, date, or numeric input.'],
            ['• Gray background   = Optional field. Leave empty if not applicable.'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
        ]);
        foreach ([3, 14] as $row) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            ]);
        }
        $sheet->getStyle('A15:D15')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
        ]);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(82);
        return [];
    }
}

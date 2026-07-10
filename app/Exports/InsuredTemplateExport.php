<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\CostScheme;
use App\Models\Coverage;
use App\Models\OperativeDoc;
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

class InsuredTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $operativeDocs,
        private readonly array $costSchemes,
        private readonly array $companies,
        private readonly array $coverages,
    ) {}

    public function sheets(): array
    {
        return [
            new InsuredsDataSheet(),
            new BiRefOperativeDocsSheet($this->operativeDocs),
            new BiRefCostSchemesSheet($this->costSchemes),
            new BiRefCompaniesSheet($this->companies),
            new BiRefCoveragesSheet($this->coverages),
            new BiReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            operativeDocs: OperativeDoc::orderBy('business_code')->orderBy('id')
                ->get(['id', 'business_code', 'description'])
                ->toArray(),
            costSchemes: CostScheme::orderBy('id')
                ->get(['id', 'agreement_type', 'description'])
                ->toArray(),
            companies: Company::orderBy('name')
                ->get(['id', 'name'])
                ->toArray(),
            coverages: Coverage::orderBy('name')
                ->get(['id', 'name', 'acronym'])
                ->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — Insureds (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class InsuredsDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'Insureds'; }

    public function headings(): array
    {
        return [
            'op_document_id', // A  FK → operative_docs.id, required
            'cscheme_id',     // B  FK → cost_schemes.id, required
            'company_name',   // C  FK → companies.name, required
            'coverage_name',  // D  FK → coverages.name, required
            'premium',        // E  float, required
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 5, null));
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 22, 'C' => 36, 'D' => 30, 'E' => 18];
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

        // A — op_document_id: yellow (parent FK key)
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'    => ['size' => 8.5, 'bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // B — cscheme_id: blue (FK)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // C — company_name: blue (FK lookup)
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // D — coverage_name: blue (FK lookup)
        $sheet->getStyle("D2:D{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // E — premium: required numeric (white)
        $sheet->getStyle("E2:E{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "INSUREDS IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  op_document_id  REQUIRED · FK → operative_docs.id. See REF_OperativeDocs sheet.\n" .
            "B  cscheme_id      REQUIRED · FK → cost_schemes.id. See REF_CostSchemes sheet.\n" .
            "C  company_name    REQUIRED · FK → companies.name. See REF_Companies sheet.\n" .
            "D  coverage_name   REQUIRED · FK → coverages.name. See REF_Coverages sheet.\n" .
            "E  premium         REQUIRED · Numeric (e.g. 1000000.00).\n" .
            "\nNote: id (UUID) is assigned automatically. Each row creates a new record."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Operative Docs
// ─────────────────────────────────────────────────────────────────────────────

class BiRefOperativeDocsSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_OperativeDocs'; }
    public function headings(): array { return ['op_document_id (use in column A)', 'Business Code', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [
            $r['id'],
            $r['business_code'],
            mb_substr($r['description'] ?? '', 0, 100),
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
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(55);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 3 — REF: Cost Schemes
// ─────────────────────────────────────────────────────────────────────────────

class BiRefCostSchemesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_CostSchemes'; }
    public function headings(): array { return ['cscheme_id (use in column B)', 'Agreement Type', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [
            $r['id'],
            $r['agreement_type'] ?? '',
            mb_substr($r['description'] ?? '', 0, 100),
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
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(50);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 4 — REF: Companies
// ─────────────────────────────────────────────────────────────────────────────

class BiRefCompaniesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Companies'; }
    public function headings(): array { return ['ID', 'Name (use in column C)']; }
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
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(45);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 5 — REF: Coverages
// ─────────────────────────────────────────────────────────────────────────────

class BiRefCoveragesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Coverages'; }
    public function headings(): array { return ['ID', 'Name (use in column D)', 'Acronym']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['id'], $r['name'], $r['acronym'] ?? ''], $this->rows);
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
        $sheet->getStyle("C2:C{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(14);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 6 — README
// ─────────────────────────────────────────────────────────────────────────────

class BiReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['INSUREDS IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the Insureds sheet.'],
            ['• Each row creates a new insured record. There is no update/upsert — duplicate rows will create duplicate records.'],
            ['• op_document_id must match an existing operative document id (see REF_OperativeDocs sheet).'],
            ['• cscheme_id must match an existing cost scheme id (see REF_CostSchemes sheet).'],
            ['• company_name must match exactly a Name in REF_Companies sheet.'],
            ['• coverage_name must match exactly a Name in REF_Coverages sheet.'],
            ['• id (UUID) is assigned automatically — do not include it.'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'op_document_id', 'YES', 'Must match an existing operative document id. See REF_OperativeDocs sheet.'],
            ['B', 'cscheme_id',     'YES', 'Must match an existing cost scheme id. See REF_CostSchemes sheet.'],
            ['C', 'company_name',   'YES', 'Must match exactly a Name from REF_Companies sheet.'],
            ['D', 'coverage_name',  'YES', 'Must match exactly a Name from REF_Coverages sheet.'],
            ['E', 'premium',        'YES', 'Numeric premium amount (e.g. 1000000.00).'],
            [''],
            ['COLOR CODING'],
            ['• Yellow background = Parent FK key (op_document_id). Must match an existing operative document.'],
            ['• Blue background   = FK lookup column. Use the reference sheets to find valid values.'],
            ['• White background  = Required numeric input.'],
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
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(78);
        return [];
    }
}

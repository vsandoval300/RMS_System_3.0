<?php

namespace App\Exports;

use App\Models\CostScheme;
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

class DocSchemeTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $operativeDocs,
        private readonly array $costSchemes,
    ) {}

    public function sheets(): array
    {
        return [
            new DocSchemesDataSheet(),
            new DsRefOperativeDocsSheet($this->operativeDocs),
            new DsRefCostSchemesSheet($this->costSchemes),
            new DsReadmeSheet(),
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
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — DocSchemes (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class DocSchemesDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'DocSchemes'; }

    public function headings(): array
    {
        return [
            'op_document_id', // A  FK → operative_docs.id, required
            'cscheme_id',     // B  FK → cost_schemes.id, required
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 2, null));
    }

    public function columnWidths(): array
    {
        return ['A' => 24, 'B' => 24];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = self::EMPTY_ROWS + 1;

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getStyle('A1:B1')->applyFromArray([
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

        // B — cscheme_id: blue (FK lookup)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'    => ['size' => 8.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "DOCUMENT COST SCHEMES IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  op_document_id  REQUIRED · FK → operative_docs.id. See REF_OperativeDocs sheet.\n" .
            "B  cscheme_id      REQUIRED · FK → cost_schemes.id. See REF_CostSchemes sheet.\n" .
            "\nNote: id (UUID) and index are assigned automatically. Each row creates a new record."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Operative Docs
// ─────────────────────────────────────────────────────────────────────────────

class DsRefOperativeDocsSheet implements FromArray, WithHeadings, WithStyles, WithTitle
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

class DsRefCostSchemesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
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
// Sheet 4 — README
// ─────────────────────────────────────────────────────────────────────────────

class DsReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['DOCUMENT COST SCHEMES IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the DocSchemes sheet.'],
            ['• Each row creates a new document cost scheme record. There is no update/upsert — duplicate rows will create duplicate records.'],
            ['• op_document_id must match an existing operative document id (see REF_OperativeDocs sheet).'],
            ['• cscheme_id must match an existing cost scheme id (see REF_CostSchemes sheet).'],
            ['• id (UUID) and index are assigned automatically — do not include them.'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'op_document_id', 'YES', 'Must match an existing operative document id. See REF_OperativeDocs sheet.'],
            ['B', 'cscheme_id',     'YES', 'Must match an existing cost scheme id. See REF_CostSchemes sheet.'],
            [''],
            ['COLOR CODING'],
            ['• Yellow background = Parent FK key (op_document_id). Must match an existing operative document.'],
            ['• Blue background   = FK lookup column. Use the reference sheets to find valid values.'],
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

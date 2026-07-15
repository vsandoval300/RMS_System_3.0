<?php

namespace App\Exports;

use App\Models\Currency;
use App\Models\Partner;
use App\Models\Region;
use App\Models\Reinsurer;
use App\Models\Treaty;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BusinessTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $reinsurers,
        private readonly array $partners,
        private readonly array $currencies,
        private readonly array $regions,
        private readonly array $treaties,
    ) {}

    public function sheets(): array
    {
        return [
            new BusinessesDataSheet(),
            new BusinessRefReinsurersSheet($this->reinsurers),
            new BusinessRefPartnersSheet($this->partners),
            new BusinessRefCurrenciesSheet($this->currencies),
            new BusinessRefRegionsSheet($this->regions),
            new BusinessRefTreatiesSheet($this->treaties),
            new BusinessReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            reinsurers:  Reinsurer::orderBy('name')->get(['id', 'name'])->toArray(),
            partners:    Partner::orderBy('name')->get(['id', 'name'])->toArray(),
            currencies:  Currency::orderBy('acronym')->get(['acronym', 'name'])->toArray(),
            regions:     Region::orderBy('name')->get(['id', 'name'])->toArray(),
            treaties:    Treaty::orderBy('treaty_code')->get(['treaty_code', 'description'])->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — Businesses (data entry)
// ─────────────────────────────────────────────────────────────────────────────

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BusinessesDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'Businesses'; }

    public function headings(): array
    {
        return [
            'business_code',   // A  required / PK
            'source_code',     // B  optional
            'description',     // C  required
            'reinsurance_type',// D  enum
            'risk_covered',    // E  enum
            'business_type',   // F  enum
            'premium_type',    // G  enum
            'purpose',         // H  enum
            'claims_type',     // I  enum
            'reinsurer_name',  // J  FK → REF_Reinsurers
            'producer_name',   // K  FK → REF_Partners
            'currency_code',   // L  FK → REF_Currencies (ISO acronym)
            'region_name',     // M  FK → REF_Regions
            'treaty_code',     // N  optional FK
            'renewed_from',    // O  optional FK (business_code)
            'index',           // P  integer, default 1
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 16, null));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 22, 'C' => 40,
            'D' => 18, 'E' => 14, 'F' => 14,
            'G' => 14, 'H' => 14, 'I' => 20,
            'J' => 32, 'K' => 32, 'L' => 14,
            'M' => 14, 'N' => 24, 'O' => 22,
            'P' => 8,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = self::EMPTY_ROWS + 1; // +1 for header

        // Header row
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);

        // A — business_code: yellow tint = PK / key column
        $sheet->getStyle("A2:A{$last}")->applyFromArray([
            'fill'   => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fefce8']],
            'font'   => ['size' => 8.5, 'bold' => true],
            'borders'=> ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'fde68a']]],
        ]);

        // B — source_code: light gray (optional)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'   => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'   => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'borders'=> ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // C — description: plain white editable
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'      => ['size' => 8.5],
            'alignment' => ['wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // D–I — enum columns: light green tint
        $sheet->getStyle("D2:I{$last}")->applyFromArray([
            'fill'   => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'font'   => ['size' => 8.5],
            'borders'=> ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
        ]);

        // J–M — FK lookup columns: light blue tint
        $sheet->getStyle("J2:M{$last}")->applyFromArray([
            'fill'   => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eff6ff']],
            'font'   => ['size' => 8.5],
            'borders'=> ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bfdbfe']]],
        ]);

        // N–O — optional FK: gray
        $sheet->getStyle("N2:O{$last}")->applyFromArray([
            'fill'   => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'   => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'borders'=> ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // P — index: white, number
        $sheet->getStyle("P2:P{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        $sheet->freezePane('C2');

        // Instruction comment on A1
        $sheet->getComment('A1')->getText()->createTextRun(
            "BUSINESSES IMPORT TEMPLATE\n" .
            "────────────────────────────\n" .
            "A  business_code   REQUIRED · PK · upsert key\n" .
            "B  source_code     optional\n" .
            "C  description     REQUIRED\n" .
            "D  reinsurance_type  Facultative | Treaty\n" .
            "E  risk_covered      Life | Non-Life\n" .
            "F  business_type     Own | Third party\n" .
            "G  premium_type      Fixed | Estimated | Declared\n" .
            "H  purpose           Strategic | Traditional\n" .
            "I  claims_type       Claims occurrence | Claims made | Hybrid\n" .
            "J  reinsurer_name  see REF_Reinsurers sheet\n" .
            "K  producer_name   see REF_Partners sheet\n" .
            "L  currency_code   ISO code, e.g. USD  see REF_Currencies\n" .
            "M  region_name     see REF_Regions sheet\n" .
            "N  treaty_code     optional · see REF_Treaties sheet\n" .
            "O  renewed_from    optional · existing business_code\n" .
            "P  index           integer, default 1\n" .
            "\nExisting business_code → UPDATE record.\n" .
            "New business_code → INSERT record."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Reinsurers
// ─────────────────────────────────────────────────────────────────────────────

class BusinessRefReinsurersSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Reinsurers'; }
    public function headings(): array { return ['ID', 'Name (use in column J)']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['id'], $r['name']], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = count($this->rows) + 1;
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(40);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 3 — REF: Partners (Producers)
// ─────────────────────────────────────────────────────────────────────────────

class BusinessRefPartnersSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Partners'; }
    public function headings(): array { return ['ID', 'Name (use in column K)']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['id'], $r['name']], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = count($this->rows) + 1;
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
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
// Sheet 4 — REF: Currencies
// ─────────────────────────────────────────────────────────────────────────────

class BusinessRefCurrenciesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Currencies'; }
    public function headings(): array { return ['Code / ISO (use in column L)', 'Name']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['acronym'], $r['name']], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = count($this->rows) + 1;
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(35);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 5 — REF: Regions
// ─────────────────────────────────────────────────────────────────────────────

class BusinessRefRegionsSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Regions'; }
    public function headings(): array { return ['ID', 'Name (use in column M)']; }
    public function array(): array
    {
        return array_map(fn($r) => [$r['id'], $r['name']], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = count($this->rows) + 1;
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8.5]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 6 — REF: Treaties
// ─────────────────────────────────────────────────────────────────────────────

class BusinessRefTreatiesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_Treaties'; }
    public function headings(): array { return ['Treaty Code (use in column N)', 'Description']; }
    public function array(): array
    {
        return array_map(fn($r) => [
            $r['treaty_code'],
            mb_substr($r['description'] ?? '', 0, 120),
        ], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = count($this->rows) + 1;
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getStyle("B2:B{$last}")->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(60);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 7 — README
// ─────────────────────────────────────────────────────────────────────────────

class BusinessReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['BUSINESSES IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the Businesses sheet.'],
            ['• business_code is the import key. If it already exists, the record is UPDATED. If not, it is INSERTED.'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'business_code',    'YES', 'String up to 19 chars. PK / upsert key. Example: 2026-TOB014-001'],
            ['B', 'source_code',      'NO',  'Optional identifier from another system.'],
            ['C', 'description',      'YES', 'Free text description of the business.'],
            ['D', 'reinsurance_type', 'YES', 'Facultative   |   Treaty'],
            ['E', 'risk_covered',     'YES', 'Life   |   Non-Life'],
            ['F', 'business_type',    'YES', 'Own   |   Third party'],
            ['G', 'premium_type',     'YES', 'Fixed   |   Estimated   |   Declared'],
            ['H', 'purpose',          'YES', 'Strategic   |   Traditional'],
            ['I', 'claims_type',      'YES', 'Claims occurrence   |   Claims made   |   Hybrid'],
            ['J', 'reinsurer_name',   'YES', 'Must match exactly a Name in REF_Reinsurers sheet.'],
            ['K', 'producer_name',    'YES', 'Must match exactly a Name in REF_Partners sheet.'],
            ['L', 'currency_code',    'YES', 'ISO code (e.g. USD, EUR, MXN). See REF_Currencies sheet.'],
            ['M', 'region_name',      'YES', 'Must match exactly a Name in REF_Regions sheet.'],
            ['N', 'treaty_code',      'NO',  'Optional. Must match exactly a Treaty Code in REF_Treaties.'],
            ['O', 'renewed_from',     'NO',  'Optional. Must be an existing business_code in the system.'],
            ['P', 'index',            'NO',  'Integer. Defaults to 1 if empty.'],
            [''],
            ['NOTE ON APPROVAL STATUS'],
            ['• New records are created with approval_status = Draft (DFT).'],
            ['• The import groups all records into an Import Batch (IMP-YYYY-NNNN) that starts in "Pending Review" status.'],
            ['• A manager must approve the batch from Import Batches → View → Approve Batch before the records appear in dashboards and reports.'],
            ['• Approving the batch promotes all associated businesses to Approved (APR) in a single operation.'],
            ['• Rejecting the batch soft-deletes all associated businesses and their linked documents.'],
            ['• Updating an existing record does NOT change its approval status.'],
            [''],
            ['NOTE ON LIFECYCLE STATUS'],
            ['• New records start as On Hold (no operative documents yet).'],
            ['• Lifecycle status is computed automatically and is not part of the import.'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Title
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
        ]);
        // Section headers
        foreach ([3, 9] as $row) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            ]);
        }
        // Column reference header row
        $sheet->getStyle('A10:D10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
        ]);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(70);
        return [];
    }
}

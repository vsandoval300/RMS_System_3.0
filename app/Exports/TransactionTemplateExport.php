<?php

namespace App\Exports;

use App\Models\OperativeDoc;
use App\Models\RemmitanceCode;
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

class TransactionTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $operativeDocs,
        private readonly array $remmitanceCodes,
    ) {}

    public function sheets(): array
    {
        return [
            new TransactionsDataSheet(),
            new TxRefOperativeDocsSheet($this->operativeDocs),
            new TxRefTransactionTypesSheet(),
            new TxRefTransactionStatusesSheet(),
            new TxRefRemmitanceCodesSheet($this->remmitanceCodes),
            new TxReadmeSheet(),
        ];
    }

    public static function build(): self
    {
        return new self(
            operativeDocs: OperativeDoc::orderBy('business_code')->orderBy('id')
                ->get(['id', 'business_code', 'description'])
                ->toArray(),
            remmitanceCodes: RemmitanceCode::orderBy('remmitance_code')
                ->get(['remmitance_code'])
                ->toArray(),
        );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 1 — Transactions (data entry)
// ─────────────────────────────────────────────────────────────────────────────

class TransactionsDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private const EMPTY_ROWS = 100;

    public function title(): string { return 'Transactions'; }

    public function headings(): array
    {
        return [
            'op_document_id',       // A  FK → operative_docs.id, required
            'transaction_type',     // B  enum: Premium / Claims / Claims Reserve
            'amount',               // C  decimal, required
            'proportion',           // D  decimal 0–1 (e.g. 1.000000), required
            'exch_rate',            // E  decimal (e.g. 1.0000000000), required
            'due_date',             // F  date YYYY-MM-DD, optional
            'remmitance_code',      // G  string ≤14, optional FK → remmitance_codes
            'transaction_status',   // H  enum: Pending / In process / Completed (default: Pending)
        ];
    }

    public function array(): array
    {
        return array_fill(0, self::EMPTY_ROWS, array_fill(0, 8, null));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 22, 'C' => 18,
            'D' => 16, 'E' => 20, 'F' => 18,
            'G' => 18, 'H' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = self::EMPTY_ROWS + 1;

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getStyle('A1:H1')->applyFromArray([
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

        // B — transaction_type: green (enum)
        $sheet->getStyle("B2:B{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'font'      => ['size' => 8.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
        ]);

        // C — amount: required numeric (white)
        $sheet->getStyle("C2:C{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '#,##0.00'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // D — proportion: required decimal (white)
        $sheet->getStyle("D2:D{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0.000000'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // E — exch_rate: required decimal (white)
        $sheet->getStyle("E2:E{$last}")->applyFromArray([
            'fill'         => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'font'         => ['size' => 8.5],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => '0.0000000000'],
            'borders'      => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
        ]);

        // F — due_date: optional date (gray)
        $sheet->getStyle("F2:F{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'      => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);
        $sheet->getStyle("F2:F{$last}")->getNumberFormat()->setFormatCode('YYYY-MM-DD');

        // G — remmitance_code: optional string (gray)
        $sheet->getStyle("G2:G{$last}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font'    => ['size' => 8.5, 'color' => ['rgb' => '6b7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
        ]);

        // H — transaction_status: green (enum, default Pending)
        $sheet->getStyle("H2:H{$last}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']],
            'font'      => ['size' => 8.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
        ]);

        $sheet->freezePane('B2');

        $sheet->getComment('A1')->getText()->createTextRun(
            "TRANSACTIONS IMPORT TEMPLATE\n" .
            "──────────────────────────────────────\n" .
            "A  op_document_id     REQUIRED · FK → operative_docs.id. See REF_OperativeDocs.\n" .
            "B  transaction_type   REQUIRED · Premium | Claims | Claims Reserve\n" .
            "C  amount             REQUIRED · Decimal (e.g. 1000000.00)\n" .
            "D  proportion         REQUIRED · Decimal 0–1 (e.g. 1.000000 for 100%)\n" .
            "E  exch_rate          REQUIRED · Exchange rate (e.g. 1.0000000000)\n" .
            "F  due_date           optional · Date YYYY-MM-DD\n" .
            "G  remmitance_code    optional · String ≤14. See REF_RemmitanceCodes.\n" .
            "H  transaction_status optional · Pending | In process | Completed (default: Pending)\n" .
            "\nNote: id (UUID) and index are auto-assigned. Creating a transaction also\n" .
            "auto-generates its transaction_logs via the cost scheme nodes."
        );

        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 2 — REF: Operative Docs
// ─────────────────────────────────────────────────────────────────────────────

class TxRefOperativeDocsSheet implements FromArray, WithHeadings, WithStyles, WithTitle
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
// Sheet 3 — REF: Transaction Types
// ─────────────────────────────────────────────────────────────────────────────

class TxRefTransactionTypesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function title(): string { return 'REF_TransactionTypes'; }
    public function headings(): array { return ['ID', 'Description (use in column B)']; }
    public function array(): array
    {
        return [[1, 'Premium'], [2, 'Claims'], [3, 'Claims Reserve']];
    }
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A4')->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle('B2:B4')->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 4 — REF: Transaction Statuses
// ─────────────────────────────────────────────────────────────────────────────

class TxRefTransactionStatusesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function title(): string { return 'REF_TransactionStatuses'; }
    public function headings(): array { return ['ID', 'Status (use in column H)', 'Note']; }
    public function array(): array
    {
        return [
            [1, 'Pending',    'Default — use this for new transactions'],
            [2, 'In process', 'At least one log has been processed'],
            [3, 'Completed',  'All logs are completed'],
        ];
    }
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A4')->applyFromArray(['font' => ['color' => ['rgb' => '9ca3af'], 'size' => 8.5]]);
        $sheet->getStyle('B2:B4')->applyFromArray(['font' => ['size' => 8.5, 'bold' => true]]);
        $sheet->getStyle('C2:C4')->applyFromArray(['font' => ['size' => 8, 'color' => ['rgb' => '6b7280']]]);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(45);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 5 — REF: Remmitance Codes
// ─────────────────────────────────────────────────────────────────────────────

class TxRefRemmitanceCodesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly array $rows) {}
    public function title(): string { return 'REF_RemmitanceCodes'; }
    public function headings(): array { return ['remmitance_code (use in column G)']; }
    public function array(): array
    {
        if (empty($this->rows)) {
            return [['(No remmitance codes defined yet)']];
        }
        return array_map(fn($r) => [$r['remmitance_code']], $this->rows);
    }
    public function styles(Worksheet $sheet): array
    {
        $last = max(count($this->rows) + 1, 2);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:A{$last}")->applyFromArray(['font' => ['bold' => true, 'size' => 8.5]]);
        $sheet->getColumnDimension('A')->setWidth(28);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sheet 6 — README
// ─────────────────────────────────────────────────────────────────────────────

class TxReadmeSheet implements FromArray, WithStyles, WithTitle
{
    public function title(): string { return 'README'; }

    public function array(): array
    {
        return [
            ['TRANSACTIONS IMPORT TEMPLATE — README'],
            [''],
            ['GENERAL RULES'],
            ['• Do NOT modify column headers (row 1) on the Transactions sheet.'],
            ['• Each row creates a new transaction. There is no update/upsert — duplicate rows create duplicate records.'],
            ['• id (UUID) and index are assigned automatically — do not include them.'],
            ['• op_document_id must match an existing operative document (see REF_OperativeDocs sheet).'],
            ['• IMPORTANT: Creating a transaction automatically generates its transaction_logs via the cost scheme nodes linked to the operative document. Make sure Steps 5 and 6 (Insureds and Documents Cost Schemes) are completed before importing transactions.'],
            ['• All rows are validated before import. Any error aborts the entire import.'],
            ['• Empty rows are ignored.'],
            [''],
            ['COLUMN REFERENCE'],
            ['Column', 'Field', 'Required', 'Allowed values / Notes'],
            ['A', 'op_document_id',     'YES', 'Must match an existing operative document id. See REF_OperativeDocs.'],
            ['B', 'transaction_type',   'YES', 'Premium | Claims | Claims Reserve. See REF_TransactionTypes.'],
            ['C', 'amount',             'YES', 'Decimal amount (e.g. 1000000.00).'],
            ['D', 'proportion',         'YES', 'Proportion as decimal 0–1 (e.g. 1.000000 for 100%).'],
            ['E', 'exch_rate',          'YES', 'Exchange rate decimal (e.g. 1.0000000000).'],
            ['F', 'due_date',           'NO',  'Optional due date in YYYY-MM-DD format.'],
            ['G', 'remmitance_code',    'NO',  'Optional. Must match an existing remmitance_code (max 14 chars). See REF_RemmitanceCodes.'],
            ['H', 'transaction_status', 'NO',  'Pending | In process | Completed. Defaults to Pending if empty. See REF_TransactionStatuses.'],
            [''],
            ['COLOR CODING'],
            ['• Yellow background = Parent FK key (op_document_id). Must match an existing operative document.'],
            ['• Green background  = Enum column. Only the listed values are accepted.'],
            ['• White background  = Required numeric input.'],
            ['• Gray background   = Optional field. Leave empty if not applicable.'],
            [''],
            ['TRANSACTION LOGS'],
            ['When a transaction is imported, the system automatically generates its transaction_logs'],
            ['based on the cost scheme nodes linked to the operative document (businessdoc_schemes).'],
            ['Ensure that Steps 5 (Insureds) and 6 (Documents Cost Schemes) are fully imported first.'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
        ]);
        foreach ([3, 12, 24] as $row) {
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            ]);
        }
        $sheet->getStyle('A13:D13')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
        ]);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(80);
        return [];
    }
}

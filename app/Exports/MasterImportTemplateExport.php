<?php

namespace App\Exports;

use App\Models\BusinessDocType;
use App\Models\Company;
use App\Models\Coverage;
use App\Models\Currency;
use App\Models\Deduction;
use App\Models\Partner;
use App\Models\Region;
use App\Models\Reinsurer;
use App\Models\Treaty;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterImportTemplateExport
{
    private Spreadsheet $spreadsheet;

    private const ROWS    = 200;
    private const CLR_HDR = '1e3a5f';
    private const CLR_KEY = 'fefce8'; // yellow  — PK / upsert key
    private const CLR_FK  = 'eff6ff'; // blue    — FK reference
    private const CLR_ENM = 'f0fdf4'; // green   — enum value
    private const CLR_REQ = 'ffffff'; // white   — required input
    private const CLR_OPT = 'f9fafb'; // gray    — optional input

    private function __construct(private readonly array $db) {}

    // ── Public API ─────────────────────────────────────────────────────────────

    public static function build(): self
    {
        $instance = new self([
            'reinsurers'   => Reinsurer::orderBy('name')->pluck('name')->toArray(),
            'partners'     => Partner::orderBy('name')->pluck('name')->toArray(),
            'currencies'   => Currency::orderBy('acronym')->pluck('acronym')->toArray(),
            'regions'      => Region::orderBy('name')->pluck('name')->toArray(),
            'treaties'     => Treaty::orderBy('treaty_code')->pluck('treaty_code')->toArray(),
            'docTypes'     => BusinessDocType::orderBy('name')->pluck('name')->toArray(),
            'deductions'   => Deduction::orderBy('concept')->get(['id', 'concept', 'description'])->toArray(),
            'companies'    => Company::orderBy('name')->pluck('name')->toArray(),
            'coverages'    => Coverage::orderBy('name')->pluck('name')->toArray(),
        ]);
        $instance->compose();
        return $instance;
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    // ── Composition ────────────────────────────────────────────────────────────

    private function compose(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeSheetByIndex(0); // remove default empty sheet

        // ── 8 data sheets (MUST be indices 0–7 for backend processing) ─────────
        $this->addDataSheet('Businesses', [
            'business_code', 'source_code', 'description', 'reinsurance_type',
            'risk_covered', 'business_type', 'premium_type', 'purpose',
            'claims_type', 'reinsurer_name', 'producer_name', 'currency_code',
            'region_name', 'treaty_code', 'renewed_from_business_code', 'index',
        ], [
            self::CLR_KEY, self::CLR_OPT, self::CLR_REQ, self::CLR_ENM,
            self::CLR_ENM, self::CLR_ENM, self::CLR_ENM, self::CLR_ENM,
            self::CLR_ENM, self::CLR_FK, self::CLR_FK, self::CLR_FK,
            self::CLR_FK, self::CLR_FK, self::CLR_OPT, self::CLR_OPT,
        ], [
            20, 16, 40, 18, 14, 14, 14, 16, 20, 30, 30, 14, 20, 18, 25, 8,
        ]);

        $this->addDataSheet('CostSchemes', [
            'scheme_id', 'index', 'share', 'agreement_type', 'description',
        ], [
            self::CLR_KEY, self::CLR_OPT, self::CLR_REQ, self::CLR_ENM, self::CLR_OPT,
        ], [22, 8, 12, 22, 45]);

        $this->addDataSheet('CostNodesx', [
            'cscheme_id', 'index', 'deduction_concept', 'value', 'apply_to_gross',
            'partner_source', 'partner_destination',
        ], [
            self::CLR_FK, self::CLR_OPT, self::CLR_FK, self::CLR_REQ, self::CLR_ENM,
            self::CLR_FK, self::CLR_FK,
        ], [24, 8, 14, 14, 16, 30, 30]);

        $this->addDataSheet('LiabilityStructures', [
            'business_code', 'coverage_name', 'cls', 'limit', 'limit_desc',
            'sublimit', 'sublimit_desc', 'deductible', 'deductible_desc',
        ], [
            self::CLR_FK, self::CLR_FK, self::CLR_ENM, self::CLR_REQ, self::CLR_REQ,
            self::CLR_OPT, self::CLR_OPT, self::CLR_OPT, self::CLR_OPT,
        ], [20, 28, 8, 14, 30, 14, 30, 14, 30]);

        $this->addDataSheet('OperativeDocs', [
            'id', 'business_code', 'doc_type_name', 'description',
            'inception_date', 'expiration_date', 'af_mf', 'roe_fs', 'rep_date',
        ], [
            self::CLR_KEY, self::CLR_FK, self::CLR_FK, self::CLR_REQ,
            self::CLR_REQ, self::CLR_REQ, self::CLR_REQ, self::CLR_OPT, self::CLR_OPT,
        ], [22, 20, 24, 40, 16, 16, 12, 12, 16]);

        // Date format for OperativeDocs date columns
        $od = $this->spreadsheet->getSheetByName('OperativeDocs');
        $r  = self::ROWS + 1;
        foreach (['E', 'F', 'I'] as $col) {
            $od->getStyle("{$col}2:{$col}{$r}")->getNumberFormat()->setFormatCode('YYYY-MM-DD');
        }

        $this->addDataSheet('Insureds', [
            'op_document_id', 'cscheme_id', 'company_name', 'coverage_name', 'premium',
        ], [
            self::CLR_FK, self::CLR_FK, self::CLR_FK, self::CLR_FK, self::CLR_REQ,
        ], [24, 22, 30, 28, 14]);

        $this->addDataSheet('DocSchemes', [
            'op_document_id', 'cscheme_id',
        ], [
            self::CLR_FK, self::CLR_FK,
        ], [24, 22]);

        // ── Reference sheets ──────────────────────────────────────────────────
        $this->addRefSheet('REF_Reinsurers',         ['Reinsurer Name (use in Businesses · col J)'],  array_map(fn($n) => [$n], $this->db['reinsurers']));
        $this->addRefSheet('REF_Partners',            ['Partner Name (use in Businesses · col K, CostNodesx · col F-G)'], array_map(fn($n) => [$n], $this->db['partners']));
        $this->addRefSheet('REF_Currencies',          ['Acronym (use in Businesses · col L)'],        array_map(fn($n) => [$n], $this->db['currencies']));
        $this->addRefSheet('REF_Regions',             ['Region Name (use in Businesses · col M)'],    array_map(fn($n) => [$n], $this->db['regions']));
        $this->addRefSheet('REF_Treaties',            ['Treaty Code (use in Businesses · col N)'],    array_map(fn($n) => [$n], $this->db['treaties']));
        $this->addRefSheet('REF_DocTypes',            ['Doc Type Name (use in OperativeDocs · col C)'], array_map(fn($n) => [$n], $this->db['docTypes']));
        $this->addRefSheet('REF_Deductions',          ['Concept (use in CostNodesx · col C)', 'ID', 'Description'], array_map(fn($d) => [$d['concept'], $d['id'], $d['description'] ?? ''], $this->db['deductions']));
        $this->addRefSheet('REF_Companies',           ['Company Name (use in Insureds · col C)'],     array_map(fn($n) => [$n], $this->db['companies']));
        $this->addRefSheet('REF_Coverages',           ['Coverage Name (use in LiabilityStructures · col B and Insureds · col D)'], array_map(fn($n) => [$n], $this->db['coverages']));
        // ── Cross-sheet Data Validations ──────────────────────────────────────
        $this->addValidations();

        // ── README (last tab) ─────────────────────────────────────────────────
        $this->addReadmeSheet();

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    // ── Data sheet builder ─────────────────────────────────────────────────────

    private function addDataSheet(string $title, array $headers, array $colors, array $widths): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($title);

        $cols = $this->colLetters(count($headers));
        $last = self::ROWS + 1;

        // Header row
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '1', $h);
        }
        $sheet->getStyle('A1:' . end($cols) . '1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CLR_HDR]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '374151']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Data area
        foreach ($cols as $i => $col) {
            $sheet->getStyle("{$col}2:{$col}{$last}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $colors[$i] ?? self::CLR_REQ]],
                'font'      => ['size' => 8.5],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'e5e7eb']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getColumnDimension($col)->setWidth($widths[$i] ?? 16);
        }

        $sheet->freezePane('B2');
    }

    // ── Reference sheet builder ────────────────────────────────────────────────

    private function addRefSheet(string $title, array $headers, array $rows): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($title);

        $cols = $this->colLetters(count($headers));

        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '1', $h);
        }
        foreach ($rows as $ri => $row) {
            foreach ((array) $row as $ci => $val) {
                if (isset($cols[$ci])) {
                    $sheet->setCellValue($cols[$ci] . ($ri + 2), $val);
                }
            }
        }

        $lastCol = end($cols);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CLR_HDR]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Light protection visual: gray background on the whole sheet
        $count = max(count($rows), 1);
        $sheet->getStyle("A2:{$lastCol}" . ($count + 1))->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
            'font' => ['size' => 8.5],
        ]);
    }

    // ── Cross-sheet Data Validations ───────────────────────────────────────────

    private function addValidations(): void
    {
        $r   = self::ROWS + 1;
        $biz = $this->spreadsheet->getSheetByName('Businesses');
        $cs  = $this->spreadsheet->getSheetByName('CostSchemes');
        $cn  = $this->spreadsheet->getSheetByName('CostNodesx');
        $ls  = $this->spreadsheet->getSheetByName('LiabilityStructures');
        $od  = $this->spreadsheet->getSheetByName('OperativeDocs');
        $ins = $this->spreadsheet->getSheetByName('Insureds');
        $ds  = $this->spreadsheet->getSheetByName('DocSchemes');

        // Businesses — enums
        $this->dv($biz, "D2:D{$r}", '"Facultative,Treaty"');
        $this->dv($biz, "E2:E{$r}", '"Life,Non-Life"');
        $this->dv($biz, "F2:F{$r}", '"Own,Third party"');
        $this->dv($biz, "G2:G{$r}", '"Fixed,Estimated,Declared"');
        $this->dv($biz, "H2:H{$r}", '"Strategic,Traditional"');
        $this->dv($biz, "I2:I{$r}", '"Claims occurrence,Claims made,Hybrid"');
        // Businesses — REF lookups
        $this->dv($biz, "J2:J{$r}", 'REF_Reinsurers!$A$2:$A$1000');
        $this->dv($biz, "K2:K{$r}", 'REF_Partners!$A$2:$A$1000');
        $this->dv($biz, "L2:L{$r}", 'REF_Currencies!$A$2:$A$500');
        $this->dv($biz, "M2:M{$r}", 'REF_Regions!$A$2:$A$500');
        $this->dv($biz, "N2:N{$r}", 'REF_Treaties!$A$2:$A$1000');

        // CostSchemes — enum
        $this->dv($cs, "D2:D{$r}", '"Quota Share,Surplus,Excess of Loss,Stop Loss"');

        // CostNodesx — cross-data + REF + enum
        $this->dv($cn, "A2:A{$r}", "CostSchemes!\$A\$2:\$A\${$r}");
        $this->dv($cn, "C2:C{$r}", 'REF_Deductions!$A$2:$A$50');
        $this->dv($cn, "E2:E{$r}", '"Yes,No"');
        $this->dv($cn, "F2:F{$r}", 'REF_Partners!$A$2:$A$1000');
        $this->dv($cn, "G2:G{$r}", 'REF_Partners!$A$2:$A$1000');

        // LiabilityStructures — cross-data + REF + enum
        $this->dv($ls, "A2:A{$r}", "Businesses!\$A\$2:\$A\${$r}");
        $this->dv($ls, "B2:B{$r}", 'REF_Coverages!$A$2:$A$1000');
        $this->dv($ls, "C2:C{$r}", '"Yes,No"');

        // OperativeDocs — cross-data + REF
        $this->dv($od, "B2:B{$r}", "Businesses!\$A\$2:\$A\${$r}");
        $this->dv($od, "C2:C{$r}", 'REF_DocTypes!$A$2:$A$200');

        // Insureds — cross-data + REF
        $this->dv($ins, "A2:A{$r}", "OperativeDocs!\$A\$2:\$A\${$r}");
        $this->dv($ins, "B2:B{$r}", "CostSchemes!\$A\$2:\$A\${$r}");
        $this->dv($ins, "C2:C{$r}", 'REF_Companies!$A$2:$A$1000');
        $this->dv($ins, "D2:D{$r}", 'REF_Coverages!$A$2:$A$1000');

        // DocSchemes — cross-data
        $this->dv($ds, "A2:A{$r}", "OperativeDocs!\$A\$2:\$A\${$r}");
        $this->dv($ds, "B2:B{$r}", "CostSchemes!\$A\$2:\$A\${$r}");
    }

    private function dv(Worksheet $sheet, string $range, string $formula): void
    {
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
           ->setFormula1($formula)
           ->setAllowBlank(true)
           ->setShowDropDown(false)
           ->setShowErrorMessage(true)
           ->setErrorStyle(DataValidation::STYLE_WARNING)
           ->setError('Please select a valid value from the dropdown list.');
        $sheet->setDataValidation($range, $dv);
    }

    // ── README ─────────────────────────────────────────────────────────────────

    private function addReadmeSheet(): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('README');

        $data = [
            /* 1  */ ['MASTER IMPORT TEMPLATE — All Sheets in One File'],
            /* 2  */ [''],
            /* 3  */ ['Fill the 8 data sheets in order (left to right). Empty sheets are skipped. All sheets must be error-free before any data is inserted.'],
            /* 4  */ [''],
            /* 5  */ ['Sheet', 'DB Table', 'Strategy', 'Notes'],
            /* 6  */ ['Businesses',          'businesses',           'Upsert by business_code', 'Core insurance contract'],
            /* 7  */ ['CostSchemes',         'cost_schemes',         'Upsert by scheme_id',     'Pricing structure header'],
            /* 8  */ ['CostNodesx',          'cost_nodexs',          'Upsert by scheme + index','Individual cost nodes (parties)'],
            /* 9  */ ['LiabilityStructures', 'liability_structures', 'INSERT only',             'Coverage limits and deductibles'],
            /* 10 */ ['OperativeDocs',       'operative_docs',       'Upsert by id',            'Policy documents (id ≤19 chars, e.g. BSNS-01)'],
            /* 11 */ ['Insureds',            'businessdoc_insureds', 'INSERT only',             'Insured parties per document'],
            /* 12 */ ['DocSchemes',          'businessdoc_schemes',  'INSERT only',             'Cost scheme assignments (auto index)'],
            /* 13 */ [''],
            /* 15 */ ['CROSS-SHEET DROPDOWNS'],
            /* 16 */ ['Columns with blue/green/yellow backgrounds show dropdown lists. Dropdowns on FK columns reference data you have entered in earlier sheets of this file. If you need to reference records already in the database (e.g., adding transactions for an existing OperativeDoc), type the ID manually — the backend validates against both the file and the database.'],
            /* 17 */ [''],
            /* 18 */ ['COLOR CODING (all data sheets)'],
            /* 19 */ ['Yellow', 'Key / upsert ID column'],
            /* 20 */ ['Blue',   'FK reference — must match a value in an earlier data sheet or already in the database'],
            /* 21 */ ['Green',  'Enum — only the listed values are accepted'],
            /* 22 */ ['White',  'Required input (numeric or text)'],
            /* 23 */ ['Gray',   'Optional — leave blank if not applicable'],
        ];

        foreach ($data as $ri => $row) {
            foreach ($row as $ci => $val) {
                $col = $this->colLetters($ci + 1)[0];
                if ($ci > 0) {
                    $col = chr(ord('A') + $ci);
                }
                $sheet->setCellValue($col . ($ri + 1), $val);
            }
        }

        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::CLR_HDR]]]);
        $sheet->getStyle('A5:D5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CLR_HDR]],
        ]);
        $sheet->getStyle('A15')->applyFromArray(['font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']]]);
        $sheet->getStyle('A18')->applyFromArray(['font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']]]);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(75);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function colLetters(int $count): array
    {
        $cols = [];
        for ($i = 0; $i < $count; $i++) {
            $cols[] = chr(ord('A') + $i);
        }
        return $cols;
    }
}

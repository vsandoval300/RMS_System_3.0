<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperativeDocsExport implements
    FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    protected Collection $rows;
    protected int $rowIndex = 0;
    protected string $reportDate;
    protected int $maxNodes = 0;

    public function __construct(Collection $rows, int $maxNodes = 0)
    {
        $this->rows       = $rows->values();
        $this->maxNodes   = max(0, (int) $maxNodes);
        $this->reportDate = Carbon::now()->format('Y-m-d');
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        $base = [
            'Reg_Num',
            'Report_Date',

            'Business Code', 'OperativeDoc ID', 'Document Type',
            'Id_Reinsurer', 'Reinsurer_name', 'Short name', 'Currency', 'roe_fs',
            'Share (%)', 'Created Date', 'Inception Date', 'Expiration Date', 'Coverage Days',
            'Premium Type', 'Claims Type', 'Placement Type',
            'Max Limit Liab', 'Insured Name', 'Country', 'Coverage',

            // ✅ OC renombrado
            'GWP_Annualised_oc', 'GWP_ftp_oc', 'GWP_fts_oc',

            'Cost_Scheme_ID',
        ];

        // ✅ Bloque dinámico 1: columnas del nodo (3 por nodo)
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Deduction_Type";
            $base[] = "Node_{$i}_Source";
            $base[] = "Node_{$i}_Value";
        }

        // ✅ Bloque OC: Amount por nodo
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Amount_oc";
        }
        $base[] = 'Total_Discounts_oc';
        $base[] = 'Net_GWP_oc';

        // ✅ Bloque USD: GWP + Amounts divididos por roe_fs
        $base[] = 'GWP_Annualised_usd';
        $base[] = 'GWP_ftp_usd';
        $base[] = 'GWP_fts_usd';

        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Amount_usd";
        }
        $base[] = 'Total_Discounts_usd';
        $base[] = 'Net_GWP_usd';

        return $base;
    }

    public function map($doc): array
    {
        $created    = optional($doc->created_at)?->format('Y-m-d');
        $inception  = optional($doc->inception_date)?->format('Y-m-d');
        $expiration = optional($doc->expiration_date)?->format('Y-m-d');

        $coverageDays = ($doc->inception_date && $doc->expiration_date)
            ? Carbon::parse($doc->inception_date)->diffInDays(Carbon::parse($doc->expiration_date))
            : null;

        $maxLimitLiab = 0.0;
        foreach ($doc->business?->liabilityStructures ?? [] as $ls) {
            $limit = (float) ($ls->limit ?? 0);
            $cls   = (float) ($ls->cls ?? 0);
            if ($cls > 1) {
                $cls /= 100;
            }
            $maxLimitLiab += $limit * $cls;
        }

        $placementType = ($doc->business?->renewed_from_id) ? 'Renewal' : 'New';

        // ==========================
        // ✅ GWP OC (siempre numérico)
        // ==========================
        $premiumOc = (float) ($doc->insured_premium ?? 0);

        $premiumFtpOc = (!is_null($coverageDays) && $coverageDays > 0)
            ? ($premiumOc / 365) * (float) $coverageDays
            : 0.0;

        $share = (float) ($doc->share ?? 0);

        // Nota: si share viene 12 en vez de 0.12, normalizamos
        $share = $this->normalizeRate($share);

        $premiumFtsOc = $premiumFtpOc * $share;

        // roe_fs
        $roe = (float) ($doc->roe_fs ?? 0);
        $roeValid = ($roe > 0);

        // ==========================
        // ✅ GWP USD (si roe inválido => 0)
        // ==========================
        $premiumUsd    = $roeValid ? ($premiumOc / $roe) : 0.0;
        $premiumFtpUsd = $roeValid ? ($premiumFtpOc / $roe) : 0.0;
        $premiumFtsUsd = $roeValid ? ($premiumFtsOc / $roe) : 0.0;

        // Reinsurer Id rule: CNS if exists else id
        $reinsurer   = $doc->business?->reinsurer;
        $idReinsurer = null;

        if ($reinsurer) {
            $cns = $reinsurer->cns_reinsurer ?? null;
            $idReinsurer = (!is_null($cns) && trim((string) $cns) !== '')
                ? $cns
                : ($reinsurer->id ?? null);
        }

        $row = [
            ++$this->rowIndex,
            $this->reportDate,

            $doc->business?->business_code ?? '-',
            $doc->id,
            $doc->docType?->name ?? '-',

            $idReinsurer,
            $reinsurer?->name ?? '-',
            $reinsurer?->short_name ?? '-',

            $doc->business?->currency?->acronym ?? '-',
            $doc->roe_fs ?? null,

            $share, // ya normalizado
            $created,
            $inception,
            $expiration,
            $coverageDays,

            $doc->business?->premium_type ?? '-',
            $doc->business?->claims_type ?? '-',
            $placementType,

            $maxLimitLiab,
            $doc->insured_name ?? '-',
            $doc->country_name ?? '-',
            $doc->coverage_name ?? '-',

            // ✅ OC (siempre número)
            $premiumOc,
            $premiumFtpOc,
            $premiumFtsOc,

            $doc->insured_cscheme_id ?? '-',
        ];

        // ✅ Nodos (3 columnas por nodo)
        $nodes = is_array($doc->nodes_list ?? null) ? $doc->nodes_list : [];

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $row[] = is_array($n) ? ($n['deduction_type'] ?? null) : null;
            $row[] = is_array($n) ? ($n['source'] ?? null) : null;

            // ✅ Value siempre numérico (cero si no hay nodo)
            $value = (is_array($n) && array_key_exists('value', $n))
                ? (float) $n['value']
                : 0.0;

            $row[] = $value;
        }

        // =========================================================
        // ✅ BLOQUE OC: Amounts + Totales (cero si no hay nodo)
        // =========================================================
        $totalDiscountsOc = 0.0;

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $rateRaw = (is_array($n) && array_key_exists('value', $n))
                ? (float) $n['value']
                : 0.0;

            $rate = $this->normalizeRate($rateRaw);

            $amountOc = (float) $premiumFtsOc * (float) $rate;

            $row[] = $amountOc;
            $totalDiscountsOc += $amountOc;
        }

        $netGwpOc = (float) $premiumFtsOc - (float) $totalDiscountsOc;

        $row[] = $totalDiscountsOc;
        $row[] = $netGwpOc;

        // =========================================================
        // ✅ BLOQUE USD: GWP + Amounts + Totales
        // =========================================================
        $row[] = $premiumUsd;
        $row[] = $premiumFtpUsd;
        $row[] = $premiumFtsUsd;

        $totalDiscountsUsd = 0.0;

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $rateRaw = (is_array($n) && array_key_exists('value', $n))
                ? (float) $n['value']
                : 0.0;

            $rate = $this->normalizeRate($rateRaw);

            $amountUsd = (float) $premiumFtsUsd * (float) $rate;

            $row[] = $amountUsd;
            $totalDiscountsUsd += $amountUsd;
        }

        $netGwpUsd = (float) $premiumFtsUsd - (float) $totalDiscountsUsd;

        $row[] = $totalDiscountsUsd;
        $row[] = $netGwpUsd;

        return $row;
    }

    public function columnFormats(): array
    {
        $formats = [
            'K' => NumberFormat::FORMAT_PERCENTAGE_00, // Share (%)
            'L' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Created
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Inception
            'N' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Expiration
            'S' => '#,##0.00',                         // Max Limit Liab
        ];

        // Cost_Scheme_ID está en Z (según tu layout base)
        $costSchemeColLetter = 'Z';
        $firstNodeColIndex   = $this->excelColToIndex($costSchemeColLetter) + 1; // AA

        // Node_*_Value (tercera col por nodo)
        for ($i = 0; $i < $this->maxNodes; $i++) {
            $valueColIndex = $firstNodeColIndex + ($i * 3) + 2;
            $formats[$this->indexToExcelCol($valueColIndex)] = '#,##0.00';
        }

        // Amount_oc block
        $firstAmountOcIndex = $firstNodeColIndex + ($this->maxNodes * 3);
        for ($i = 0; $i < $this->maxNodes; $i++) {
            $formats[$this->indexToExcelCol($firstAmountOcIndex + $i)] = '#,##0.00';
        }

        $totalDiscountsOcIndex = $firstAmountOcIndex + $this->maxNodes;
        $netGwpOcIndex         = $totalDiscountsOcIndex + 1;

        $formats[$this->indexToExcelCol($totalDiscountsOcIndex)] = '#,##0.00';
        $formats[$this->indexToExcelCol($netGwpOcIndex)]         = '#,##0.00';

        // USD GWP (3 cols)
        $gwpUsdStartIndex = $netGwpOcIndex + 1;
        $formats[$this->indexToExcelCol($gwpUsdStartIndex + 0)] = '#,##0.00';
        $formats[$this->indexToExcelCol($gwpUsdStartIndex + 1)] = '#,##0.00';
        $formats[$this->indexToExcelCol($gwpUsdStartIndex + 2)] = '#,##0.00';

        // Amount_usd
        $firstAmountUsdIndex = $gwpUsdStartIndex + 3;
        for ($i = 0; $i < $this->maxNodes; $i++) {
            $formats[$this->indexToExcelCol($firstAmountUsdIndex + $i)] = '#,##0.00';
        }

        $totalDiscountsUsdIndex = $firstAmountUsdIndex + $this->maxNodes;
        $netGwpUsdIndex         = $totalDiscountsUsdIndex + 1;

        $formats[$this->indexToExcelCol($totalDiscountsUsdIndex)] = '#,##0.00';
        $formats[$this->indexToExcelCol($netGwpUsdIndex)]         = '#,##0.00';

        return $formats;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }

    /**
     * Normaliza valores tipo porcentaje:
     * - 4.5  => 0.045
     * - 0.045 => 0.045
     */
    private function normalizeRate(float $value): float
    {
        return ($value > 1) ? ($value / 100) : $value;
    }

    private function excelColToIndex(string $col): int
    {
        $col = strtoupper($col);
        $len = strlen($col);
        $num = 0;

        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($col[$i]) - 64);
        }

        return $num;
    }

    private function indexToExcelCol(int $index): string
    {
        $col = '';
        while ($index > 0) {
            $rem = ($index - 1) % 26;
            $col = chr(65 + $rem) . $col;
            $index = intdiv($index - 1, 26);
        }
        return $col;
    }
}
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

            'Cost_Scheme_ID',
        ];

        // ✅ Bloque dinámico 1: columnas del nodo (3 por nodo)
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Deduction_Type";
            $base[] = "Node_{$i}_Source";
            $base[] = "Node_{$i}_Value";
        }

        // ✅ GWP (Original Currency) ANTES del bloque Amount
        $base[] = 'GWP_Annualised_oc';
        $base[] = 'GWP_ftp_oc';
        $base[] = 'GWP_fts_oc';

        // ✅ Bloque dinámico 2: Amount OC (1 por nodo)
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Amount_oc"; // = GWP_fts_oc * Node_i_Value
        }

        $base[] = 'Total_Discounts_oc';
        $base[] = 'Net_GWP_oc';

        // ✅ Bloque USD (dividir entre roe_fs)
        $base[] = 'GWP_Annualised_usd';
        $base[] = 'GWP_ftp_usd';
        $base[] = 'GWP_fts_usd';

        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Amount_usd"; // = GWP_fts_usd * Node_i_Value
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
            if ($cls > 1) $cls /= 100;
            $maxLimitLiab += $limit * $cls;
        }

        $placementType = ($doc->business?->renewed_from_id) ? 'Renewal' : 'New';

        // ----------------------------
        // GWP base (Original Currency)
        // ----------------------------
        $premiumOc = is_null($doc->insured_premium) ? null : (float) $doc->insured_premium;

        $premiumFtpOc = (!is_null($premiumOc) && !is_null($coverageDays))
            ? ($premiumOc / 365) * (float) $coverageDays
            : null;

        $share = is_null($doc->share) ? null : (float) $doc->share;

        $premiumFtsOc = (!is_null($premiumFtpOc) && !is_null($share))
            ? $premiumFtpOc * $share
            : null;

        // ----------------------------
        // Roe (para USD)
        // ----------------------------
        $roe = is_null($doc->roe_fs) ? null : (float) $doc->roe_fs;
        $roeOk = (!is_null($roe) && $roe > 0);

        $premiumUsd = !is_null($premiumOc)
            ? ($roeOk ? ($premiumOc / $roe) : 0.0)
            : null;

        $premiumFtpUsd = !is_null($premiumFtpOc)
            ? ($roeOk ? ($premiumFtpOc / $roe) : 0.0)
            : null;

        $premiumFtsUsd = !is_null($premiumFtsOc)
            ? ($roeOk ? ($premiumFtsOc / $roe) : 0.0)
            : null;

        // Reinsurer Id rule: CNS if exists else id
        $reinsurer   = $doc->business?->reinsurer;
        $idReinsurer = null;

        if ($reinsurer) {
            $cns = $reinsurer->cns_reinsurer ?? null;
            $idReinsurer = (!is_null($cns) && trim((string) $cns) !== '')
                ? $cns
                : ($reinsurer->id ?? null);
        }

        // ----------------------------
        // Base fijo (hasta Coverage)
        // ----------------------------
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

            $share,
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

            $doc->insured_cscheme_id ?? '-',
        ];

        // ----------------------------
        // Nodos (3 columnas por nodo)
        // ----------------------------
        $nodes = is_array($doc->nodes_list ?? null) ? $doc->nodes_list : [];

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $row[] = is_array($n) ? ($n['deduction_type'] ?? null) : null;
            $row[] = is_array($n) ? ($n['source'] ?? null) : null;
            $row[] = (is_array($n) && array_key_exists('value', $n)) ? $n['value'] : null;
        }

        // ----------------------------
        // GWP OC (renombrado)
        // ----------------------------
        $row[] = $premiumOc;
        $row[] = $premiumFtpOc;
        $row[] = $premiumFtsOc;

        // ----------------------------
        // Amounts OC + Totales OC
        // ----------------------------
        $totalDiscountsOc = 0.0;

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $rateRaw = (is_array($n) && array_key_exists('value', $n)) ? $n['value'] : null;
            $rate    = is_null($rateRaw) ? null : $this->normalizeRate((float) $rateRaw);

            $amountOc = (!is_null($premiumFtsOc) && !is_null($rate))
                ? ((float) $premiumFtsOc * (float) $rate)
                : null;

            $row[] = $amountOc;

            if (!is_null($amountOc)) {
                $totalDiscountsOc += (float) $amountOc;
            }
        }

        $row[] = $totalDiscountsOc; // ✅ siempre número (cero si no hay)
        $row[] = !is_null($premiumFtsOc)
            ? ((float) $premiumFtsOc - (float) $totalDiscountsOc)
            : null;

        // ----------------------------
        // GWP USD
        // ----------------------------
        $row[] = $premiumUsd;
        $row[] = $premiumFtpUsd;
        $row[] = $premiumFtsUsd;

        // ----------------------------
        // Amounts USD + Totales USD
        // ----------------------------
        $totalDiscountsUsd = 0.0;

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $rateRaw = (is_array($n) && array_key_exists('value', $n)) ? $n['value'] : null;
            $rate    = is_null($rateRaw) ? null : $this->normalizeRate((float) $rateRaw);

            $amountUsd = (!is_null($premiumFtsUsd) && !is_null($rate))
                ? ((float) $premiumFtsUsd * (float) $rate)
                : null;

            $row[] = $amountUsd;

            if (!is_null($amountUsd)) {
                $totalDiscountsUsd += (float) $amountUsd;
            }
        }

        $row[] = $totalDiscountsUsd; // ✅ siempre número
        $row[] = !is_null($premiumFtsUsd)
            ? ((float) $premiumFtsUsd - (float) $totalDiscountsUsd)
            : null;

        return $row;
    }

    public function columnFormats(): array
    {
        $formats = [];

        // Construimos un map label => letra para no depender de posiciones fijas.
        $labels = $this->headings();
        foreach ($labels as $i => $label) {
            $colLetter = $this->indexToExcelCol($i + 1);

            // % Share
            if ($label === 'Share (%)') {
                $formats[$colLetter] = NumberFormat::FORMAT_PERCENTAGE_00;
                continue;
            }

            // Fechas
            if (in_array($label, ['Created Date', 'Inception Date', 'Expiration Date'], true)) {
                $formats[$colLetter] = NumberFormat::FORMAT_DATE_YYYYMMDD;
                continue;
            }

            // Números (montos)
            $isMoney =
                $label === 'roe_fs' ||
                $label === 'Max Limit Liab' ||
                str_starts_with($label, 'GWP_') ||
                str_starts_with($label, 'Node_') && (
                    str_contains($label, '_Value') ||
                    str_contains($label, '_Amount_')
                ) ||
                str_contains($label, 'Total_Discounts_') ||
                str_contains($label, 'Net_GWP_');

            if ($isMoney) {
                $formats[$colLetter] = '#,##0.00';
                continue;
            }
        }

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
     * Normaliza un valor de nodo:
     * - si viene como 4.5 => 0.045
     * - si viene como 0.045 => 0.045
     */
    private function normalizeRate(float $value): float
    {
        return ($value > 1) ? ($value / 100) : $value;
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
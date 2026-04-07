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
    protected int $maxNodes = 0;

    public function __construct(Collection $rows, int $maxNodes = 0)
    {
        $this->rows       = $rows->values();
        $this->maxNodes   = max(0, (int) $maxNodes);
       
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        $base = [
            'Reg_Num',
            'Created By',
            'Rep_Date',

            'Business Code', 'OperativeDoc ID', 'Document Type',
            'Source Code', 'Producer', 'Parent', 'Renewed from',
            'Id_Reinsurer', 'Reinsurer_name', 'Short name', 'Currency', 'roe_fs',
            'Share (%)', 'Created Date', 'Inception Date', 'Expiration Date', 'Coverage Days',
            'Premium Type', 'Claims Type', 'Placement Type',
            'Max Limit Liab', 'Insured Name', 'Country', 'Coverage',

            // ✅ verde primero (como lo quieres)
            'Cost_Scheme_ID',
        ];

        // ✅ Bloque Node_* (Deduction/Source/Value)
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Deduction_Type";
            $base[] = "Node_{$i}_Source";
            $base[] = "Node_{$i}_Value";
        }

        // ✅✅✅ CHANGE [ORDER-1]: agregar correctamente las 3 amarillas AQUÍ
        $base[] = 'GWP_Annualised_oc';
        $base[] = 'GWP_ftp_oc';
        $base[] = 'GWP_fts_oc';

        // ✅ Bloque Node_* Amount_oc
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Amount_oc";
        }

        $base[] = 'Total_Discounts_oc';
        $base[] = 'Net_GWP_oc';

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
        $repDate = optional($doc->rep_date)?->format('Y-m-d');

        $coverageDays = ($doc->inception_date && $doc->expiration_date)
            ? Carbon::parse($doc->inception_date)->diffInDays(Carbon::parse($doc->expiration_date))
            : null;

        $maxLimitLiab = 0.0;

        foreach ($doc->business?->liabilityStructures ?? [] as $ls) {
            $limit = (float) ($ls->limit ?? 0);

            // 🔵 ignorar completamente el campo cls
            $maxLimitLiab += $limit;
        }

        $placementType = ($doc->business?->renewed_from_id) ? 'Renewal' : 'New';

        // ==========================
        // ✅ GWP OC
        // ==========================
        $premiumOc = (float) ($doc->insured_premium ?? 0);

        $premiumFtpOc = (!is_null($coverageDays) && $coverageDays > 0)
            ? ($premiumOc / 365) * (float) $coverageDays
            : 0.0;

        $share = (float) ($doc->share ?? 0);
        $share = $this->normalizeRate($share);

        $premiumFtsOc = $premiumFtpOc * $share;

        // roe_fs
        $roe      = (float) ($doc->roe_fs ?? 0);
        $roeValid = ($roe > 0);

        // USD
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

        // Nodos base (3 cols por nodo)
        $nodes = is_array($doc->nodes_list ?? null) ? $doc->nodes_list : [];

        // =========================================================
        // ✅✅✅ CHANGE [MAP-ORDER-1]: Aquí reordenamos para que empate con headings()
        // Orden: base -> (verde) Cost_Scheme_ID -> Nodes (3 cols) -> (amarillas) 3 GWP OC -> Amounts...
        // =========================================================
        $row = [
            ++$this->rowIndex,
            $doc->created_by_initials ?? '-',
            $repDate,

            $doc->business?->business_code ?? '-',
            $doc->id,
            $doc->docType?->name ?? '-',

            $doc->business_source_code ?? '-',
            $doc->producer_name ?? '-',
            $doc->business_parent_id ?? '-',
            $doc->business_renewed_from_id ?? '-',

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

            // ✅ (verde) va AQUÍ (tal como está en headings)
            $doc->insured_cscheme_id ?? '-',
        ];

        // =========================================================
        // ✅✅✅ CHANGE [MAP-ORDER-2]: Primero van los Node_* (Deduction/Source/Value)
        // =========================================================
        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $row[] = is_array($n) ? ($n['deduction_type'] ?? null) : null;
            $row[] = is_array($n) ? ($n['source'] ?? null) : null;

            $value = (is_array($n) && array_key_exists('value', $n))
                ? (float) $n['value']
                : 0.0;

            $row[] = $value;
        }

        // =========================================================
        // ✅✅✅ CHANGE [MAP-ORDER-3]: Ahora van las 3 amarillas (GWP OC)
        // =========================================================
        $row[] = $premiumOc;
        $row[] = $premiumFtpOc;
        $row[] = $premiumFtsOc;

        // =========================================================
        // ✅ OC Amounts (usa amount_oc ya calculado en $wide)
        // =========================================================
        $totalDiscountsOc = 0.0;

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $amountOc = (is_array($n) && array_key_exists('amount_oc', $n))
                ? round((float) $n['amount_oc'], 2)
                : 0.0;

            $row[] = $amountOc;
            $totalDiscountsOc += $amountOc;
        }

        $totalDiscountsOc = round($totalDiscountsOc, 2);
        $netGwpOc         = round((float) $premiumFtsOc - (float) $totalDiscountsOc, 2);

        $row[] = $totalDiscountsOc;
        $row[] = $netGwpOc;

        // =========================================================
        // ✅ USD Amounts (amount_oc / roe_fs)
        // =========================================================
        $row[] = $premiumUsd;
        $row[] = $premiumFtpUsd;
        $row[] = $premiumFtsUsd;

        $totalDiscountsUsd = 0.0;

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $amountOc = (is_array($n) && array_key_exists('amount_oc', $n))
                ? round((float) $n['amount_oc'], 2)
                : 0.0;

            $amountUsd = ($roeValid && $roe > 0)
                ? round($amountOc / $roe, 2)
                : 0.0;

            $row[] = $amountUsd;
            $totalDiscountsUsd += $amountUsd;
        }

        $totalDiscountsUsd = round($totalDiscountsUsd, 2);
        $netGwpUsd         = round((float) $premiumFtsUsd - (float) $totalDiscountsUsd, 2);

        $row[] = $totalDiscountsUsd;
        $row[] = $netGwpUsd;
        

    return $row;
    }


    public function columnFormats(): array
    {
        $formats = [];

        // =========================================================
        // ✅ Robusto: ubicar columnas por NOMBRE (headings)
        // Esto evita desfaces cuando maxNodes = 0 o cambia el orden
        // =========================================================
        $headings = $this->headings();

        // helper: heading -> Excel Col (A, B, C...)
        $colOf = function (string $label) use ($headings) {
            $pos = array_search($label, $headings, true);
            return $pos === false ? null : $this->indexToExcelCol($pos + 1); // +1 porque Excel es 1-based
        };

        if ($col = $colOf('Share (%)')) {
        $formats[$col] = NumberFormat::FORMAT_PERCENTAGE_00;
        }

        if ($col = $colOf('Created Date')) {
            $formats[$col] = NumberFormat::FORMAT_DATE_YYYYMMDD;
        }

        if ($col = $colOf('Inception Date')) {
            $formats[$col] = NumberFormat::FORMAT_DATE_YYYYMMDD;
        }

        if ($col = $colOf('Expiration Date')) {
            $formats[$col] = NumberFormat::FORMAT_DATE_YYYYMMDD;
        }

        if ($col = $colOf('Max Limit Liab')) {
            $formats[$col] = '#,##0.00';
        }
        
        // =========================================================
        // ✅ Node_*_Value en porcentaje con decimales dinámicos
        // =========================================================
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            if ($col = $colOf("Node_{$i}_Value")) {
                $formats[$col] = '0.################%';
            }
        }

        // =========================================================
        // ✅ Node_* Amount_oc y Amount_usd como montos
        // =========================================================
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            if ($col = $colOf("Node_{$i}_Amount_oc")) {
                $formats[$col] = '#,##0.00';
            }
            if ($col = $colOf("Node_{$i}_Amount_usd")) {
                $formats[$col] = '#,##0.00';
            }
        }

        // =========================================================
        // ✅ Montos OC (incluye las 3 amarillas + totals)
        // =========================================================
        foreach ([
            'GWP_Annualised_oc',
            'GWP_ftp_oc',
            'GWP_fts_oc',
            'Total_Discounts_oc',
            'Net_GWP_oc',
        ] as $h) {
            if ($col = $colOf($h)) {
                $formats[$col] = '#,##0.00';
            }
        }

        // =========================================================
        // ✅ Montos USD (incluye las 3 que te faltaban al final)
        // =========================================================
        foreach ([
            'GWP_Annualised_usd',
            'GWP_ftp_usd',
            'GWP_fts_usd',
            'Total_Discounts_usd',
            'Net_GWP_usd',
        ] as $h) {
            if ($col = $colOf($h)) {
                $formats[$col] = '#,##0.00';
            }
        }

        // =========================================================
        // ✅ NEW: Rep_Date al final como fecha
        // (asegúrate de agregar 'Rep_Date' en headings() y map())
        // =========================================================
        if ($col = $colOf('Rep_Date')) {
            $formats[$col] = NumberFormat::FORMAT_DATE_YYYYMMDD;
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
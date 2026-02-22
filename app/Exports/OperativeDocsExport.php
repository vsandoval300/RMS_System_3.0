<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OperativeDocsExport implements
    FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
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

            'Business Code','OperativeDoc ID','Document Type',
            'Id_Reinsurer','Reinsurer_name','Short name','Currency','roe_fs',
            'Share (%)','Created Date','Inception Date','Expiration Date','Coverage Days',
            'Premium Type','Claims Type','Placement Type',
            'Max Limit Liab','Insured Name','Country','Coverage',
            'GWP_Annualised','GWP_ftp','GWP_fts',
            'Cost_Scheme_ID',
        ];

        // ✅ Columnas dinámicas por nodos (3 por nodo)
        for ($i = 1; $i <= $this->maxNodes; $i++) {
            $base[] = "Node_{$i}_Deduction_Type";
            $base[] = "Node_{$i}_Source";
            $base[] = "Node_{$i}_Value";
        }

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

        // GWP base (Annualised)
        $premium = is_null($doc->insured_premium) ? null : (float) $doc->insured_premium;

        // FTP
        $premiumFtp = (!is_null($premium) && !is_null($coverageDays))
            ? ($premium / 365) * (float) $coverageDays
            : null;

        // Share (%)
        $share = is_null($doc->share) ? null : (float) $doc->share;

        // FTS
        $premiumFts = (!is_null($premiumFtp) && !is_null($share))
            ? $premiumFtp * $share
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

        $base = [
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

            $premium,
            $premiumFtp,
            $premiumFts,

            $doc->insured_cscheme_id ?? '-',
        ];

        // ✅ Nodos (3 columnas por nodo)
        $nodes = is_array($doc->nodes_list ?? null) ? $doc->nodes_list : [];

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $n = $nodes[$i] ?? null;

            $base[] = is_array($n) ? ($n['deduction_type'] ?? null) : null;
            $base[] = is_array($n) ? ($n['source'] ?? null) : null;
            $base[] = (is_array($n) && array_key_exists('value', $n)) ? $n['value'] : null;
        }

        return $base;
    }

    public function columnFormats(): array
    {
        $formats = [
            'K' => NumberFormat::FORMAT_PERCENTAGE_00, // Share (%)
            'L' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Created
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Inception
            'N' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Expiration
            'S' => '#,##0.00',                         // Max Limit Liab
            'W' => '#,##0.00',                         // GWP_Annualised
            'X' => '#,##0.00',                         // GWP_ftp
            'Y' => '#,##0.00',                         // GWP_fts
        ];

        // Después de GWP_fts (Y) viene Cost_Scheme_ID (Z)
        // Nodos empiezan en AA: Node_1_Deduction_Type (AA), Node_1_Source (AB), Node_1_Value (AC)
        $firstNodeColIndex = $this->excelColToIndex('Z') + 1; // AA

        for ($i = 0; $i < $this->maxNodes; $i++) {
            $valueColIndex = $firstNodeColIndex + ($i * 3) + 2; // value = +2
            $colLetter = $this->indexToExcelCol($valueColIndex);
            $formats[$colLetter] = '#,##0.00';
        }

        return $formats;
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
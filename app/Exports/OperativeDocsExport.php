<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\{FromCollection,WithHeadings,WithMapping,ShouldAutoSize,WithColumnFormatting};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OperativeDocsExport implements
    FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    protected Collection $rows;
    protected Collection $partners;
    protected Collection $concepts;
    protected int $rowIndex = 0;
    protected string $reportDate;

    public function __construct(Collection $rows, Collection $partners, Collection $concepts)
    {
        $this->rows     = $rows->values();
        $this->partners = $partners->values();
        $this->concepts = $concepts->values();
        $this->reportDate = Carbon::now()->format('Y-m-d');
    }

    public function collection(): Collection { return $this->rows; }

    public function headings(): array
    {
        $base = [
            'Index','Business Code','OperativeDoc ID','Document Type',
            'Reinsurer_name','Short name','Currency','Share (%)',
            'Created Date','Inception Date','Expiration Date','Coverage Days',
            'Premium Type','Claims Type','Placement Type','Report Date',
            'Max Limit Liab','Insured Name','Country','Coverage','Premium',
        ];

        // Partner × Concepto (encabezados planos)
        $dyn = [];
        foreach ($this->partners as $p) {
            $pLabel = Str::of($p)->replace(['|','/','\\',':',';'], ' - ')->limit(40);
            foreach ($this->concepts as $c) {
                $cLabel = Str::of($c)->replace(['|','/','\\',':',';'], ' - ')->limit(40);
                $dyn[] = "{$pLabel} – {$cLabel}";
            }
        }

        return array_merge($base, $dyn);
    }

    public function map($doc): array
    {
        // --- lo que ya tenías (resumido) ---
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

        $base = [
            ++$this->rowIndex,
            $doc->business?->business_code ?? '-',
            $doc->id,
            $doc->docType?->name ?? '-',
            $doc->business?->reinsurer?->name ?? '-',
            $doc->business?->reinsurer?->short_name ?? '-',
            $doc->business?->currency?->acronym ?? '-',
            is_null($doc->share) ? null : (float) $doc->share,
            $created,
            $inception,
            $expiration,
            $coverageDays,
            $doc->business?->premium_type ?? '-',
            $doc->business?->claims_type ?? '-',
            $placementType,
            $this->reportDate,
            $maxLimitLiab,
            $doc->insured_name ?? '-',
            $doc->country_name ?? '-',
            $doc->coverage_name ?? '-',
            is_null($doc->insured_premium) ? null : (float) $doc->insured_premium,
        ];

        // Partner × Concepto: tomar de la matriz $doc->pc_matrix (puede no existir)
        $dyn = [];
        $matrix = (array) ($doc->pc_matrix ?? []);
        foreach ($this->partners as $p) {
            $rowByConcept = $matrix[$p] ?? [];
            foreach ($this->concepts as $c) {
                $val = $rowByConcept[$c] ?? null;
                $dyn[] = is_null($val) ? null : (float) $val;
            }
        }

        return array_merge($base, $dyn);
    }

    public function columnFormats(): array
    {
        // Las columnas base conocidas:
        return [
            'H' => NumberFormat::FORMAT_PERCENTAGE_00, // Share
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Created
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Inception
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Expiration
            'P' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Report
            'Q' => '#,##0.00',                         // Max Limit
            'U' => '#,##0.00',                         // Premium
            // Las dinámicas (Partner–Concept) quedan en formato general;
            // si quieres #,##0.00 para todas, avísame y lo ajusto con WithEvents.
        ];
    }
}

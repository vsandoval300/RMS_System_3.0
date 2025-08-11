<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Carbon;

class OperativeDocsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting
{
    protected Collection $docs;
    protected int $rowIndex = 0;
    protected string $reportDate;

    public function __construct(Collection $docs)
    {
        $this->docs = $docs->values();
        $this->reportDate = Carbon::now()->format('Y-m-d');
    }

    public function collection(): Collection
    {
        return $this->docs;
    }

   public function headings(): array
    {
        return [
            'Index',
            'Business Code',
            'OperativeDoc ID',
            'Document Type',
            'Reinsurer_name',
            'Short name',
            'Currency',
            'Share (%)',
            'Created Date',
            'Inception Date',
            'Expiration Date',
            'Coverage Days',
            'Premium Type',
            'Claims Type',
            'Placement Type',
            'Report Date',
            'Max Limit Liab',
            'Insured Name',   // R
            'Country',        // 👈 S (countries.name)
        ];
    }

    public function map($doc): array
    {
        $created    = optional($doc->created_at)?->format('Y-m-d');
        $inception  = optional($doc->inception_date)?->format('Y-m-d');
        $expiration = optional($doc->expiration_date)?->format('Y-m-d');

        $coverageDays = null;
        if ($doc->inception_date && $doc->expiration_date) {
            $coverageDays = Carbon::parse($doc->inception_date)
                ->diffInDays(Carbon::parse($doc->expiration_date));
        }

        // Σ(limit * cls) por negocio
        $maxLimitLiab = 0.0;
        $lsList = $doc->business?->liabilityStructures ?? collect();
        foreach ($lsList as $ls) {
            $limit = (float) ($ls->limit ?? 0);
            $cls   = (float) ($ls->cls ?? 0);
            if ($cls > 1) { $cls = $cls / 100; }
            $maxLimitLiab += $limit * $cls;
        }

        $placementType = ($doc->business?->renewed_from_id) ? 'Renewal' : 'New';

        return [
                ++$this->rowIndex,                               // A
                $doc->business?->business_code ?? '-',           // B
                $doc->id,                                        // C
                $doc->docType?->name ?? '-',                     // D
                $doc->business?->reinsurer?->name ?? '-',        // E
                $doc->business?->reinsurer?->short_name ?? '-',  // F
                $doc->business?->currency?->acronym ?? '-',      // G
                is_null($doc->share) ? null : (float) $doc->share, // H
                $created,                                        // I
                $inception,                                      // J
                $expiration,                                     // K
                $coverageDays,                                   // L
                $doc->business?->premium_type ?? '-',            // M
                $doc->business?->claims_type ?? '-',             // N
                $placementType,                                  // O
                $this->reportDate,                               // P
                $maxLimitLiab,                                   // Q
                $doc->insured_name ?? '-',                       // R
                $doc->country_name ?? '-',                       // 👈 S
            ];
        }

    public function columnFormats(): array
    {
        // A..S
        return [
            'H' => NumberFormat::FORMAT_PERCENTAGE_00, // Share
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Created
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Inception
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Expiration
            'P' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Report
            'Q' => '#,##0.00',                         // Max Limit Liab
            // R (Insured Name) y S (Country) son texto
        ];
    }
}
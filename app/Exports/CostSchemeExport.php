<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

class CostSchemeExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
{
    protected Collection $rows;
    protected int $rowIndex = 0;
    protected string $reportDate;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows->values();
        $this->reportDate = Carbon::now()->format('Y-m-d');
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Reg_Num',
            'Report_Date',

            // ===== Scheme =====
            'Scheme_ID',
            'Daily_Index',
            'Agreement_Type',
            'Description',
            'Share (%)',
            'Created_At',
            'Updated_At',
            'Created_By',

            // ===== Node =====
            'Node_Index',
            'Deduction_Type',
            'Value (%)',
            'Apply_To_Gross',
            'Partner_Source',
            'Partner_Destination',
        ];
    }

    public function map($row): array
    {
        $scheme = $row->scheme;
        $node   = $row->node;

        return [
            ++$this->rowIndex,
            $this->reportDate,

            $scheme->id,
            $scheme->index,
            $scheme->agreement_type,
            $scheme->description,

            (float) ($scheme->share ?? 0), // decimal (0.12)

            optional($scheme->created_at)?->format('Y-m-d H:i:s'),
            optional($scheme->updated_at)?->format('Y-m-d H:i:s'),
            $scheme->createdBy?->name,

            $node?->index,
            $node?->deduction?->concept,

            $node ? (float) ($node->value ?? 0) : null, // decimal (0.045)

            $node ? (bool) ($node->apply_to_gross ?? false) : null,

            $node?->partnerSource?->short_name ?? $node?->partnerSource?->name,
            $node?->partnerDestination?->short_name ?? $node?->partnerDestination?->name,
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Share (%) -> columna F
            'F' => NumberFormat::FORMAT_PERCENTAGE_00,

            // Value (%) -> columna L
            'L' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MissingPdfsReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
{
    public $timeout = 1200;
    public $tries   = 3;

    protected $rows;
    protected int $rowIndex = 0;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return [
            '#',
            'Business Code',
            'Business Description',
            'Reinsurer',
            'Created By',
            'Business Created At',
            'Document Code',
            'Doc Type',
            'Doc Description',
            'Inception Date',
            'Expiration Date',
            'Doc Status',
            'Doc Created At',
            'Days Without PDF',
        ];
    }

    public function map($row): array
    {
        $inception  = $row->inception_date  ? Carbon::parse($row->inception_date)  : null;
        $expiration = $row->expiration_date ? Carbon::parse($row->expiration_date) : null;
        $docCreated = $row->doc_created_at  ? Carbon::parse($row->doc_created_at)  : null;

        $status = match (true) {
            $inception  === null                            => '—',
            now()->lt($inception)                          => 'Pending',
            $expiration !== null && now()->lte($expiration) => 'In Force',
            default                                        => 'Expired',
        };

        $daysWithoutPdf = $docCreated ? (int) now()->diffInDays($docCreated) : null;

        return [
            ++$this->rowIndex,
            $row->business_code,
            $row->business_description,
            $row->reinsurer_name ?? '—',
            $row->created_by_name ?? '—',
            $row->business_created_at ? Carbon::parse($row->business_created_at)->format('Y-m-d') : '—',
            $row->doc_id,
            $row->doc_type_name ?? '—',
            $row->doc_description ?? '—',
            $inception  ? $inception->format('Y-m-d')  : '—',
            $expiration ? $expiration->format('Y-m-d') : '—',
            $status,
            $docCreated ? $docCreated->format('Y-m-d') : '—',
            $daysWithoutPdf,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'N' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

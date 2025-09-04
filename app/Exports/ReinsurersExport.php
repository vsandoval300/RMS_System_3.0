<?php
// app/Exports/ReinsurersExport.php
namespace App\Exports;

use App\Models\Reinsurer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReinsurersExport implements FromCollection, WithHeadings
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records->map(function ($r) {
            return [
                'ID'               => $r->id,
                'LSK'              => $r->cns_reinsurer,
                'Name'             => $r->name,
                'Short Name'       => $r->short_name,
                'Parent'           => $r->parent?->name,
                'Acronym'          => $r->acronym,
                'Class'            => $r->class,
                'Established'      => $r->established,
                'Manager'          => $r->manager?->name,
                'Country'          => $r->country?->alpha_3,
                'Type'             => $r->reinsurer_type?->type_acronym,
                'Operative Status' => $r->operative_status?->acronym,
                'Created At'       => $r->created_at?->format('Y-m-d'),
                'Updated At'       => $r->updated_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'LSK',
            'Name',
            'Short Name',
            'Parent',
            'Acronym',
            'Class',
            'Established',
            'Manager',
            'Country',
            'Type',
            'Operative Status',
            'Created At',
            'Updated At',
        ];
    }
}

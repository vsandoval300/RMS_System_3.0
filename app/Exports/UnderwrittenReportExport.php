<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class UnderwrittenReportExport implements FromCollection
{
    /**
     * @return Collection
     */
    public function collection()
    {
        //
    }
}

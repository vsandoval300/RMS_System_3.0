<?php

namespace App\Services;

use App\Models\OperativeDoc;
use App\Models\CostScheme;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PremiumForPeriodService
{
    public function anualFTS(?int $reinsurerId = null, ?int $year = null): array
    {
        $query = OperativeDoc::query()
            ->whereYear('rep_date', '>=', 2010)
            ->with([
                'docType',
                'business.currency',
                'schemes.costScheme.costNodexes.partnerSource',
                'schemes.costScheme.costNodexes.deduction',
                'insureds.company.country',
                'insureds.coverage'
            ])

             // filtra solo si $reinsurerId tiene valor
            ->when($reinsurerId, fn($q) => $q->whereHas('business', fn($b) => $b->where('reinsurer_id', $reinsurerId)))
            // filtra solo si $year tiene valor
            ->when($year, fn($q) => $q->whereYear('rep_date', $year));

            /* if ($reinsurerId !== null) {
                $query->whereHas('business', function ($q) use ($reinsurerId) {
                    $q->where('reinsurer_id', $reinsurerId);
                });
            } */

        $docs = $query->get();
        
        $grouped = [];

        foreach ($docs as $doc) {

            $year = \Carbon\Carbon::parse($doc->rep_date)->year;

            $inception = \Carbon\Carbon::parse($doc->inception_date);
            $expiration = \Carbon\Carbon::parse($doc->expiration_date);

            $daysInYear = $inception->isLeapYear() ? 366 : 365;
            $coverageDays = $inception->diffInDays($expiration);

            $totalPremium = $doc->insureds->sum('premium');

            $ftp = ($daysInYear > 0)
                ? ($totalPremium / $daysInYear) * $coverageDays
                : 0;
            
            $fts = 0;

            foreach ($doc->insureds as $insured) {

                $share = optional(
                    $doc->schemes
                        ->firstWhere('costScheme.id', $insured->cscheme_id)
                )->costScheme->share ?? 0;

                $ftpIndividual = ($daysInYear > 0)
                    ? ($insured->premium / $daysInYear) * $coverageDays
                    : 0;

                $fts += $ftpIndividual * $share;

                $totalConvertedPremium = ($doc->roe_fs > 0)
                    ? ($fts / $doc->roe_fs)
                    : 0;
            }

            if (!isset($grouped[$year])) {
                $grouped[$year] = [
                    'ftp' => 0,
                    'fts' => 0,
                ];
            }

            $grouped[$year]['ftp'] += $ftp;
            $grouped[$year]['fts'] += $totalConvertedPremium;

        }

            ksort($grouped);

            return [
            'labels' => array_keys($grouped),
            'ftp'    => array_values(array_column($grouped, 'ftp')),
            'fts'    => array_values(array_column($grouped, 'fts')),
        ];

    }


    public function monthlyByYear(?int $reinsurerId = null, array $years = []): array
    {
        if (empty($years)) {
            $years = [now()->year];
        }

        $months = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
            7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'
        ];

        $query = DB::table('operative_docs as d')

            ->join('businessdoc_insureds as i','i.op_document_id','=','d.id')

            ->join('businessdoc_schemes as s','s.op_document_id','=','d.id')

            ->join('cost_schemes as cs','cs.id','=','s.cscheme_id')

            ->join('businesses as b','b.business_code','=','d.business_code')

            ->selectRaw('
                EXTRACT(YEAR FROM d.rep_date) as year,
                EXTRACT(MONTH FROM d.rep_date) as month,

                SUM(
                    (
                        (i.premium /
                            CASE
                                WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                                THEN 366
                                ELSE 365
                            END
                        )
                        *
                        DATE_PART(\'day\', d.expiration_date - d.inception_date)
                    )
                    *
                    cs.share
                    /
                    NULLIF(d.roe_fs,0)
                ) as fts
            ')

            ->whereIn(DB::raw('EXTRACT(YEAR FROM d.rep_date)'), $years)

            ->when($reinsurerId, fn($q) =>
                $q->where('b.reinsurer_id',$reinsurerId)
            )

            ->groupByRaw('year, month')

            ->orderByRaw('year, month');

        $rows = $query->get();

        /*
        |--------------------------------------------------------------------------
        | Transformar resultado para ChartJS
        |--------------------------------------------------------------------------
        */

        $grouped = [];

        foreach ($years as $year) {
            $grouped[$year] = array_fill(1,12,0);
        }

        foreach ($rows as $row) {

            $year = (int)$row->year;
            $month = (int)$row->month;

            $grouped[$year][$month] = (float)$row->fts;
        }

        // Define colores para las líneas
        $colors = [
            '#FF6384', // rojo
            '#36A2EB', // azul
            '#FFCE56', // amarillo
            '#4BC0C0', // verde
            '#9966FF', // morado
            '#FF9F40', // naranja
            '#8A2BE2', // azul violeta
            '#00CED1', // turquesa
            '#FF4500', // naranja fuerte
            '#228B22', // verde bosque
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($grouped as $year => $monthsData) {
            $color = $colors[$colorIndex % count($colors)];

            $datasets[] = [
                'label' => (string)$year,
                'data' => array_values($monthsData),
                'borderColor' => $color,
                //'backgroundColor' => $color . '80', // 50% opacidad
                'fill' => false,
                'tension' => 0.3, // para líneas suavizadas (opcional)
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
            ];

            $colorIndex++;
        }

        return [
            'labels' => array_values($months),
            'datasets' => $datasets,
        ];
    }

    // Función equivalente a monthlyFTSByYear pero para "Business"
    public function monthlyBusinessByYear(?int $reinsurerId = null, array $years = []): array
    {
        if (empty($years)) {
            $years = [now()->year];
        }

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $query = DB::table('operative_docs as d')
            ->join('businessdoc_insureds as i', 'i.op_document_id', '=', 'd.id')
            ->join('businesses as b', 'b.business_code', '=', 'd.business_code')
            ->selectRaw('
                EXTRACT(YEAR FROM d.rep_date) as year,
                EXTRACT(MONTH FROM d.rep_date) as month,
                SUM(i.premium) as total_business
            ')
            ->whereIn(DB::raw('EXTRACT(YEAR FROM d.rep_date)'), $years)
            ->when($reinsurerId, fn($q) =>
                $q->where('b.reinsurer_id', $reinsurerId)
            )
            ->groupByRaw('year, month')
            ->orderByRaw('year, month');

        $rows = $query->get();

        // Inicializa todos los meses con 0
        $grouped = [];
        foreach ($years as $year) {
            $grouped[$year] = array_fill(1, 12, 0);
        }

        foreach ($rows as $row) {
            $year = (int)$row->year;
            $month = (int)$row->month;
            $grouped[$year][$month] = (float)$row->total_business;
        }

        // Colores
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#8A2BE2', '#00CED1',
            '#FF4500', '#228B22',
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($grouped as $year => $monthsData) {
            $color = $colors[$colorIndex % count($colors)];

            $datasets[] = [
                'label' => (string)$year,
                'data' => array_values($monthsData),
                'borderColor' => $color,
                'backgroundColor' => $color . '80', // 50% opacidad
                'fill' => false,
                'tension' => 0.3,
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
            ];

            $colorIndex++;
        }

        return [
            'labels' => array_values($months),
            'datasets' => $datasets,
        ];
    }

    public function monthlyFTSByYear(?int $reinsurerId = null, array $years = []): array
    {
        if (empty($years)) {
            $years = [now()->year];
        }

        $months = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
            7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'
        ];

        /**
         * 🔥 Subquery 1: Agrupar insureds
         */
        $insuredsSub = DB::table('businessdoc_insureds')
            ->selectRaw('
                op_document_id,
                cscheme_id,
                SUM(premium) as premium
            ')
            ->groupBy('op_document_id', 'cscheme_id');

        /**
         * 🔥 Subquery 2: Agrupar schemes
         */
        $schemesSub = DB::table('businessdoc_schemes as s')
            ->join('cost_schemes as cs', 'cs.id', '=', 's.cscheme_id')
            ->selectRaw('
                s.op_document_id,
                s.cscheme_id,
                SUM(cs.share) as share
            ')
            ->groupBy('s.op_document_id', 's.cscheme_id');

        /**
         * 🔥 BASE: cálculo por fila (nivel OperativeDoc ID)
         */
        $baseQuery = DB::table('operative_docs as d')
            ->joinSub($insuredsSub, 'i', function ($join) {
                $join->on('i.op_document_id', '=', 'd.id');
            })
            ->leftJoinSub($schemesSub, 's', function ($join) {
                $join->on('s.op_document_id', '=', 'i.op_document_id')
                    ->on('s.cscheme_id', '=', 'i.cscheme_id');
            })
            ->join('businesses as b', 'b.business_code', '=', 'd.business_code')
            ->selectRaw('
                d.id as op_id,

                EXTRACT(YEAR FROM d.rep_date) as year,
                EXTRACT(MONTH FROM d.rep_date) as month,

                ROUND(
                    (
                        (i.premium / 365)
                        *
                        DATE_PART(\'day\', d.expiration_date - d.inception_date)
                        *
                        COALESCE(s.share, 1)
                        /
                        NULLIF(d.roe_fs, 0)
                    )::numeric,
                    12
                ) as fts_piece
            ')
            ->whereIn(DB::raw('EXTRACT(YEAR FROM d.rep_date)'), $years)
            ->when($reinsurerId, fn($q) =>
                $q->where('b.reinsurer_id', $reinsurerId)
            );

        /**
         * 🔥 STEP 1: Agrupar por OperativeDoc ID
         */
        $byDocument = DB::query()
            ->fromSub($baseQuery, 't')
            ->selectRaw('
                year,
                month,
                op_id,
                SUM(fts_piece) as fts_doc
            ')
            ->groupBy('year', 'month', 'op_id');

        /**
         * 🔥 STEP 2: Suma final mensual
         */
        $finalQuery = DB::query()
            ->fromSub($byDocument, 'x')
            ->selectRaw('
                year,
                month,
                SUM(fts_doc) as fts
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month');

        $rows = $finalQuery->get();

        /**
         * 🔥 Formato ChartJS
         */
        $grouped = [];
        foreach ($years as $year) {
            $grouped[$year] = array_fill(1,12,0);
        }

        foreach ($rows as $row) {
            $grouped[(int)$row->year][(int)$row->month] = (float)$row->fts;
        }

        $colors = [
            '#FF6384','#36A2EB','#FFCE56','#4BC0C0',
            '#9966FF','#FF9F40','#8A2BE2','#00CED1',
            '#FF4500','#228B22',
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($grouped as $year => $monthsData) {
            $color = $colors[$colorIndex % count($colors)];

            $datasets[] = [
                'label' => (string)$year,
                'data' => array_values($monthsData),
                'borderColor' => $color,
                'fill' => false,
                'tension' => 0.3,
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
            ];

            $colorIndex++;
        }

        return [
            'labels' => array_values($months),
            'datasets' => $datasets,
        ];
    }

    public function monthlyFTS(?int $reinsurerId = null, array $years = []): array
    {
        if (empty($years)) {
            $years = [now()->year];
        }

        $months = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
            7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'
        ];

        $query = OperativeDoc::query()
            ->with([
            'business',
            'schemes.costScheme',
            'insureds',
            ])
            ->when($reinsurerId, fn($q) =>
                $q->whereHas('business', fn($b) =>
                $b->where('reinsurer_id', $reinsurerId)
                )
            )->whereIn(DB::raw('EXTRACT(YEAR FROM rep_date)'), $years);

        $docs = $query->get();

        // Inicializar estructura
        $grouped = [];
        foreach ($years as $year) {
            $grouped[$year] = array_fill(1, 12, 0);
        }

        foreach ($docs as $doc) {

            $year = Carbon::parse($doc->rep_date)->year;
            $month = Carbon::parse($doc->rep_date)->month;

            $inception = Carbon::parse($doc->inception_date);
            $expiration = Carbon::parse($doc->expiration_date);

            $daysInYear = $inception->isLeapYear() ? 366 : 365;

            // 🔴 FIX IMPORTANTE
            $coverageDays = $inception->diffInDays($expiration);

            $fts = 0;

            foreach ($doc->insureds as $insured) {

                $scheme = $doc->schemes
                    ->firstWhere('costScheme.id', $insured->cscheme_id);

                $share = $scheme?->costScheme?->share ?? 0;

                $ftpIndividual = ($daysInYear > 0)
                ? ($insured->premium / $daysInYear) * $coverageDays
                : 0;

                $fts += $ftpIndividual * $share;
            }

            $ftsConverted = ($doc->roe_fs > 0)
            ? ($fts / $doc->roe_fs)
            : 0;

           
            if (isset($grouped[$year][$month])) {
                $grouped[$year][$month] += $ftsConverted;
            }
        }

        // Formato ChartJS
        $colors = [
            '#FF6384','#36A2EB','#FFCE56','#4BC0C0',
            '#9966FF','#FF9F40','#8A2BE2','#00CED1',
        ];

        $datasets = [];
        $i = 0;

        foreach ($grouped as $year => $monthsData) {

            $datasets[] = [
                'label' => (string)$year,
                'data' => array_values($monthsData),
                'borderColor' => $colors[$i % count($colors)],
                'fill' => false,
                'tension' => 0.3,
            ];

            $i++;
        }

        return [
            'labels' => array_values($months),
            'datasets' => $datasets,
        ];
    }
}

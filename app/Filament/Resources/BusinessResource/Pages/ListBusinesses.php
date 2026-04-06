<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Exports\OperativeDocsExport;
use App\Filament\Resources\BusinessResource;
use App\Filament\Resources\BusinessResource\Widgets\BusinessStatsOverview;
use App\Models\OperativeDoc;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Hidden;

class ListBusinesses extends ListRecords
{
    protected static string $resource = BusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ Export primero (antes de New Business)
        // ✅ Export primero (antes de New Business)
        Action::make('export')
            ->label('Export Report')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->modalHeading('Export Reports')
            ->modalSubmitActionLabel('Generate')
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->form([
                Select::make('report_type')
                    ->label('Report Type')
                    ->options([
                        'operative_docs'      => 'Underwritten – Coverage Period',
                        'underwritten_report' => 'Underwritten – Reporting Month',
                    ])
                    ->default('operative_docs')
                    ->required()
                    ->live()
                    ->helperText(function ($get) {
                        return match ($get('report_type')) {
                            'operative_docs' => '📅 Retrieves information for businesses whose operative documents have a coverage period within the selected date range.',
                            'underwritten_report' => '📊 Retrieves information for businesses whose operative documents have a reporting date within the selected date range.',
                            default => null,
                        };
                    }),

                Select::make('reinsurer_ids')
                    ->label('Reinsurer(s)')
                    ->placeholder('All reinsurers')
                    ->options(fn () => \App\Models\Reinsurer::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Hidden::make('user_timezone')
                    ->default('America/Mexico_City')
                    ->extraAttributes([
                        'x-init' => '$el.value = Intl.DateTimeFormat().resolvedOptions().timeZone'
                    ]),      

                // =========================
                // Coverage Period (inception)
                // =========================
                DatePicker::make('from_date')
                    ->label('From date')
                    ->required(fn ($get) => $get('report_type') === 'operative_docs')
                    ->visible(fn ($get) => $get('report_type') === 'operative_docs')
                    ->native(false),

                DatePicker::make('to_date')
                    ->label('To date')
                    ->required(fn ($get) => $get('report_type') === 'operative_docs')
                    ->visible(fn ($get) => $get('report_type') === 'operative_docs')
                    ->native(false),

                // =========================
                // Reporting Month Range (rep_date)
                // =========================
                DatePicker::make('rep_from')
                    ->label('From date')
                    ->displayFormat('F Y')     // February 2026
                    ->format('Y-m-01')         // guarda día 01
                    ->required(fn ($get) => $get('report_type') === 'underwritten_report')
                    ->visible(fn ($get) => $get('report_type') === 'underwritten_report')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->live(),

                DatePicker::make('rep_to')
                    ->label('To date')
                    ->displayFormat('F Y')
                    ->format('Y-m-01')
                    ->required(fn ($get) => $get('report_type') === 'underwritten_report')
                    ->visible(fn ($get) => $get('report_type') === 'underwritten_report')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->live(),

                  
            ])
            ->action(function (array $data) {
                $report = $data['report_type'] ?? null;

                $reinsurerIds = collect($data['reinsurer_ids'] ?? [])
                    ->filter()
                    ->values();

                $scope = $reinsurerIds->isEmpty()
                    ? 'all-reinsurers'
                    : ('reinsurers-' . $reinsurerIds->implode('-'));

                $reportLabels = [
                    'operative_docs'      => 'OperativeDocs_report',
                    'underwritten_report' => 'Underwritten_report',
                ];
                $reportLabel = $reportLabels[$report] ?? ($report ?? 'report');

                // -----------------------------
                // Determine date range & column
                // -----------------------------
                if ($report === 'operative_docs') {
                    $from = $data['from_date'] ?? null;
                    $to   = $data['to_date'] ?? null;

                    if (!$from || !$to) {
                        Notification::make()
                            ->title('Please select both dates.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $rangeLabelFrom = Carbon::parse($from)->format('dMY');
                    $rangeLabelTo   = Carbon::parse($to)->format('dMY');

                    $dateColumn = 'operative_docs.inception_date';
                    $dateStart  = Carbon::parse($from)->startOfDay();
                    $dateEnd    = Carbon::parse($to)->endOfDay();
                } elseif ($report === 'underwritten_report') {
                    $repFrom = $data['rep_from'] ?? null;
                    $repTo   = $data['rep_to'] ?? null;

                    if (!$repFrom || !$repTo) {
                        Notification::make()
                            ->title('Please select a reporting month range.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $repFromC = Carbon::parse($repFrom)->startOfMonth();
                    $repToC   = Carbon::parse($repTo)->endOfMonth();

                    if ($repFromC->gt($repToC)) {
                        Notification::make()
                            ->title('Reporting month "from" must be before "to".')
                            ->warning()
                            ->send();
                        return;
                    }

                    // ✅ para el query (meses completos)
                    $dateColumn = 'operative_docs.rep_date';
                    $dateStart  = $repFromC->startOfDay();
                    $dateEnd    = $repToC->endOfDay();

                    // ✅ para el nombre del archivo: Jan2026_to_Feb2026
                    $rangeLabelFrom = $repFromC->format('dMY'); // Jan2026
                    $rangeLabelTo   = $repToC->format('dMY');   // Feb2026
                } else {
                    Notification::make()
                        ->title('Unsupported report type.')
                        ->danger()
                        ->send();
                    return;
                }

                // -----------------------------
                // Filename
                // -----------------------------
                $userTimezone = $data['user_timezone'] ?? config('app.timezone');

                $timestamp = Carbon::now($userTimezone)->format('Ymd');

                if ($report === 'underwritten_report') {
                    $filename = sprintf(
                        'UW-ReportingMonth_(between_%s_to_%s)_(%s).xlsx',
                        $rangeLabelFrom,
                        $rangeLabelTo,
                        $timestamp
                    );
                } else {
                    $filename = sprintf(
                        'UW-CoveragePeriod_(between_%s_to_%s)_(%s).xlsx',
                        $rangeLabelFrom,
                        $rangeLabelTo,
                        $timestamp
                    );
                }

                // ---------------------------------------------------------
                // Flat query: 1 registro por (insured_row_id × node)
                // ---------------------------------------------------------
                $flat = OperativeDoc::query()
                    ->with([
                        'business.reinsurer',
                        'business.currency',
                        'business.liabilityStructures',
                        'business.producer',
                        'docType',
                    ])
                    ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
                    ->leftJoin('partners as producer_partner', 'producer_partner.id', '=', 'businesses.producer_id')
                    ->when($reinsurerIds->isNotEmpty(), fn ($q) =>
                        $q->whereIn('businesses.reinsurer_id', $reinsurerIds)
                    )
                    ->whereBetween($dateColumn, [$dateStart, $dateEnd])

                    // insureds
                    ->leftJoin('businessdoc_insureds', 'businessdoc_insureds.op_document_id', '=', 'operative_docs.id')
                    ->leftJoin('companies', 'companies.id', '=', 'businessdoc_insureds.company_id')
                    ->leftJoin('countries', 'countries.id', '=', 'companies.country_id')
                    ->leftJoin('coverages', 'coverages.id', '=', 'businessdoc_insureds.coverage_id')

                    // scheme del insured
                    ->leftJoin('cost_schemes as insured_scheme', 'insured_scheme.id', '=', 'businessdoc_insureds.cscheme_id')

                    // nodes
                    ->leftJoin('cost_nodesx', 'cost_nodesx.cscheme_id', '=', 'insured_scheme.id')

                    // deductions label
                    ->leftJoin('deductions', 'deductions.id', '=', 'cost_nodesx.concept')

                    // partner source
                    ->leftJoin('partners as p_src', 'p_src.id', '=', 'cost_nodesx.partner_source_id')

                    ->orderBy('businesses.business_code')
                    ->orderBy('operative_docs.id')
                    ->orderBy('businessdoc_insureds.id')
                    ->orderBy('cost_nodesx.index')
                    ->select([
                        'operative_docs.*',

                        // ✅ NEW: fuerza a que rep_date venga como atributo accesible en el modelo
                        'operative_docs.rep_date as rep_date',

                        'businesses.source_code as business_source_code',
                        'businesses.producer_id as business_producer_id',
                        'businesses.parent_id as business_parent_id',
                        'businesses.renewed_from_id as business_renewed_from_id',
                        'producer_partner.name as producer_name',

                        'insured_scheme.share as share',

                        'companies.name as insured_name',
                        'countries.name as country_name',
                        'coverages.name as coverage_name',
                        'businessdoc_insureds.premium as insured_premium',

                        'businessdoc_insureds.id as insured_row_id',
                        'businessdoc_insureds.cscheme_id as insured_cscheme_id',

                        'cost_nodesx.id as node_id',
                        'cost_nodesx.cscheme_id as node_cscheme_id',
                        'cost_nodesx.index as node_index',
                        'cost_nodesx.value as node_value',

                        // ✅ NEW: apply_to_gross para algoritmo tipo Excel
                        'cost_nodesx.apply_to_gross as node_apply_to_gross',

                        'deductions.concept as deduction_concept',

                        'p_src.name as node_source_name',
                        'p_src.acronym as node_source_acronym',
                    ])
                    ->get();

                if ($flat->isEmpty()) {
                    Notification::make()
                        ->title('No records found for the selected range.')
                        ->info()
                        ->send();
                    return;
                }

                // ---------------------------------------------------------
                // Build wide (1 row per insured)
                // ---------------------------------------------------------
                $wide = $flat
                    ->groupBy(fn ($r) => $r->insured_row_id ?? ($r->id . '|no-insured'))
                    ->map(function ($rows) {
                        $first = $rows->first();

                        $schemeId = $first->insured_cscheme_id;

                        $schemeNodes = $rows
                            ->filter(fn ($r) => $schemeId && ($r->node_cscheme_id ?? null) === $schemeId)
                            ->unique('node_id')
                            ->sortBy(fn ($r) => (int) ($r->node_index ?? 0))
                            ->values();

                        // ✅ replicar la base exactamente como en OperativeDocsExport (premiumFtpOc * share)
                        $inception  = $first->inception_date ?? null;
                        $expiration = $first->expiration_date ?? null;

                        $coverageDays = ($inception && $expiration)
                            ? Carbon::parse($inception)->diffInDays(Carbon::parse($expiration))
                            : 0;

                        $premiumOc = (float) ($first->insured_premium ?? 0);

                        $premiumFtpOc = ($coverageDays > 0)
                            ? ($premiumOc / 365) * (float) $coverageDays
                            : 0.0;

                        $share = (float) ($first->share ?? 0);
                        $share = ($share > 1) ? ($share / 100) : $share;

                        $gwpFtsOc = round($premiumFtpOc * $share, 2);

                        // ✅ running base (gross - descuentos previos)
                        $runningBase = $gwpFtsOc;

                        $nodesList = [];

                        foreach ($schemeNodes as $r) {
                            $source = trim(($r->node_source_name ?? '') . ' - [' . ($r->node_source_acronym ?? '') . ']');
                            if ($source === '- []') {
                                $source = null;
                            }

                            $rate = is_null($r->node_value) ? 0.0 : (float) $r->node_value;
                            $rate = ($rate > 1) ? ($rate / 100) : $rate;

                            $applyToGross = (bool) ($r->node_apply_to_gross ?? false);

                            $baseForNode = $applyToGross ? $runningBase : $gwpFtsOc;

                            // ✅ Excel-like: redondeo por nodo
                            $amountOc = round($baseForNode * $rate, 2);

                            // ✅ actualizar running con valor ya redondeado
                            $runningBase = round($runningBase - $amountOc, 2);

                            $nodesList[] = [
                                'deduction_type' => $r->deduction_concept ?? null,
                                'source'         => $source ?: null,
                                'value'          => $rate,

                                // ✅ NEW
                                'apply_to_gross' => $applyToGross,
                                'amount_oc'      => $amountOc,
                            ];
                        }

                        $first->nodes_list = $nodesList;

                        return $first;
                    })
                    ->values();

                $maxNodes = (int) ($wide
                    ->map(fn ($d) => is_array($d->nodes_list ?? null) ? count($d->nodes_list) : 0)
                    ->max() ?? 0);

                return Excel::download(
                    new OperativeDocsExport($wide, $maxNodes),
                    $filename
                );
            }),




            // ✅ Tu botón existente
            Actions\CreateAction::make()
                ->label('New Business')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Business')
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BusinessStatsOverview::class,
        ];
    }
}
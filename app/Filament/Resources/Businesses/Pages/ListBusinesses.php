<?php

namespace App\Filament\Resources\Businesses\Pages;

use App\Models\Reinsurer;
use Filament\Actions\CreateAction;
use App\Exports\OperativeDocsExport;
use App\Filament\Resources\Businesses\BusinessResource;
use App\Filament\Resources\Businesses\Widgets\BusinessByYearChart;
use App\Filament\Resources\Businesses\Widgets\BusinessStatsOverview;
use App\Jobs\GenerateOperativeDocsReport;
use App\Jobs\NotifyReportReady;
use App\Models\OperativeDoc;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\LazyCollection;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Log;

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
            ->schema([
                Select::make('report_type')
                    ->label('Report Type')
                    ->options([
                        'operative_docs'      => 'Underwritten – By Inception Date',
                        'underwritten_report' => 'Underwritten – By Underwriting Month',
                    ])
                    ->default('operative_docs')
                    ->required()
                    ->live()
                    ->helperText(function ($get) {
                        return match ($get('report_type')) {
                            'operative_docs' => '📅 Retrieves information for businesses whose operative documents fall within the selected date range based on their Inception Date.',
                            'underwritten_report' => '📊 Retrieves information for businesses whose operative documents fall within the selected date range based on their Underwriting Month.',
                            default => null,
                        };
                    }),

                Select::make('reinsurer_ids')
                    ->label('Reinsurer(s)')
                    ->placeholder('All reinsurers')
                    ->options(fn () => Reinsurer::query()
                        ->whereHas('businesses')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                    )
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
                    ->values()
                    ->toArray();

                $scope = empty($reinsurerIds)
                    ? 'all-reinsurers'
                    : ('reinsurers-' . implode('-', $reinsurerIds));

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
                        'UW-ByUnderwritingMonth_(between_%s_to_%s)_(%s).xlsx',
                        $rangeLabelFrom,
                        $rangeLabelTo,
                        $timestamp
                    );
                } else {
                    $filename = sprintf(
                        'UW-ByInceptionDate_(between_%s_to_%s)_(%s).xlsx',
                        $rangeLabelFrom,
                        $rangeLabelTo,
                        $timestamp
                    );
                }

                GenerateOperativeDocsReport::dispatch(
                    $dateColumn,
                    $dateStart,
                    $dateEnd,
                    $reinsurerIds,
                    //$path,
                    $filename,
                    auth()->id()
                );

                Notification::make()
                    ->title('Report is being generated')
                    ->success()
                    ->send();
            }),




            // ✅ Tu botón existente
            CreateAction::make()
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
            BusinessByYearChart::class,
        ];
    }
    
    protected function getHeaderWidgetsData(): array
    {
        return [
            'tableFilters' => $this->tableFilters,
        ];
    }
}
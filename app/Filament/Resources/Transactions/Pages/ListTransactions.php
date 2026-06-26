<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Exports\TransactionsReportExport;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\TransactionStatus;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Reinsurer;
use App\Filament\Resources\Transactions\Widgets\TransactionStatsOverview;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->modalHeading('Export Transactions Report')
                ->modalSubmitActionLabel('Generate')
                ->schema([

                   Select::make('reinsurer_id')
                        ->label('Reinsurer')
                        ->placeholder('All reinsurers')
                        ->options(
                            collect([
                                '' => 'All Reinsurers',
                            ])->merge(
                                Reinsurer::query()
                                    ->whereHas('businesses.operativeDocs.transactions')
                                    ->orderBy('short_name')
                                    ->pluck('short_name', 'id')
                            )
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('transaction_status_id')
                        ->label('Transaction Status')
                        ->placeholder('All Statuses')
                        ->options(
                            collect([
                                0 => 'All Statuses',
                            ])->merge(
                                TransactionStatus::query()
                                    ->orderBy('transaction_status')
                                    ->pluck('transaction_status', 'id')
                            )
                        )
                        ->searchable()
                        ->preload()
                        ->default(0)
                        ->required(),

                ])
                ->action(function (array $data) {
                    $statusId = $data['transaction_status_id'] ?? 0;
                    $reinsurerId = $data['reinsurer_id'] ?? 0;

                    $statusIdForQuery = ((int) $statusId === 0) ? null : $statusId;
                    $reinsurerIdForQuery = ((int) $reinsurerId === 0) ? null : $reinsurerId;

                    $statusName = $statusIdForQuery
                        ? TransactionStatus::find($statusIdForQuery)?->transaction_status
                        : 'All_Statuses';

                    $filename = sprintf(
                        'Transactions_%s_%s.xlsx',
                        str_replace(' ', '_', $statusName),
                        Carbon::now('America/Mexico_City')->format('Ymd')
                    );

                    return Excel::download(
                        new TransactionsReportExport($statusIdForQuery, $reinsurerIdForQuery),
                        $filename
                    );
                }),

            Action::make('userManual')
                ->label('User Manual')
                ->icon('heroicon-o-book-open')
                ->color('gray')
                ->modalHeading('Instalments — User Manual')
                ->modalContent(function () {
                    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                    $disk = Storage::disk('s3');
                    $url  = $disk->temporaryUrl(
                        'user_manual/Installments_Module_Reference_v1.pdf',
                        now()->addMinutes(30),
                        [
                            'ResponseContentType'        => 'application/pdf',
                            'ResponseContentDisposition' => 'inline; filename="Instalments_User_Manual.pdf"',
                        ]
                    );
                    return view('filament.components.pdf-viewer', compact('url'));
                })
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            CreateAction::make()
                ->label('New Transaction')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Transaction')
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionStatsOverview::class,
        ];
    }
}
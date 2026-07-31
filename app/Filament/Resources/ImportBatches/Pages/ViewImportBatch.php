<?php

namespace App\Filament\Resources\ImportBatches\Pages;

use App\Filament\Resources\ImportBatches\ImportBatchResource;
use App\Models\Business;
use App\Models\BusinessOpDocsInsured;
use App\Models\BusinessOpDocsScheme;
use App\Models\CostNodex;
use App\Models\CostScheme;
use App\Models\ImportBatch;
use App\Models\LiabilityStructure;
use App\Models\OperativeDoc;
use App\Notifications\BatchReviewDecisionNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewImportBatch extends ViewRecord
{
    protected static string $resource = ImportBatchResource::class;

    public function getTitle(): string
    {
        return $this->record->batch_code;
    }

    // ── Header actions ─────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Batch')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->isPending())
                ->requiresConfirmation()
                ->modalHeading('Approve Import Batch')
                ->modalDescription("All {$this->record->totalRecords()} records from batch {$this->record->batch_code} will be marked as accepted. This cannot be undone.")
                ->modalSubmitActionLabel('Yes, approve')
                ->action(function () {
                    DB::transaction(function () {
                        Business::where('import_batch_id', $this->record->id)
                            ->update([
                                'approval_status'            => 'APR',
                                'approval_status_updated_at' => now(),
                            ]);

                        $this->record->update([
                            'status'      => 'approved',
                            'approved_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    });

                    $this->record->importer?->notify(
                        new BatchReviewDecisionNotification($this->record, 'approved', Auth::user()->name)
                    );

                    Notification::make()
                        ->success()
                        ->title("Batch {$this->record->batch_code} approved")
                        ->send();

                    $this->refreshFormData(['status', 'approved_by', 'reviewed_at']);
                }),

            Action::make('reject')
                ->label('Reject Batch')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->isPending())
                ->requiresConfirmation()
                ->modalHeading('Reject Import Batch')
                ->modalDescription("All {$this->record->totalRecords()} records from batch {$this->record->batch_code} will be soft-deleted. This can be reviewed in trash if needed.")
                ->modalSubmitActionLabel('Yes, reject and delete')
                ->schema([
                    Textarea::make('notes_reviewer')
                        ->label('Reason for rejection')
                        ->required()
                        ->rows(3)
                        ->placeholder('Describe what was wrong with this import…'),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $id = $this->record->id;

                        // Soft-delete in reverse dependency order
                        BusinessOpDocsScheme::where('import_batch_id', $id)->delete();
                        BusinessOpDocsInsured::where('import_batch_id', $id)->delete();
                        OperativeDoc::where('import_batch_id', $id)->delete();
                        LiabilityStructure::where('import_batch_id', $id)->delete();
                        CostNodex::where('import_batch_id', $id)->delete();
                        CostScheme::where('import_batch_id', $id)->delete();
                        Business::where('import_batch_id', $id)->delete();

                        $this->record->update([
                            'status'         => 'rejected',
                            'rejected_by'    => Auth::id(),
                            'notes_reviewer' => $data['notes_reviewer'],
                            'reviewed_at'    => now(),
                        ]);
                    });

                    $this->record->importer?->notify(
                        new BatchReviewDecisionNotification(
                            $this->record, 'rejected', Auth::user()->name, $data['notes_reviewer']
                        )
                    );

                    Notification::make()
                        ->danger()
                        ->title("Batch {$this->record->batch_code} rejected")
                        ->body('All imported records have been soft-deleted.')
                        ->send();

                    $this->refreshFormData(['status', 'rejected_by', 'reviewed_at', 'notes_reviewer']);
                }),
        ];
    }

    // ── Infolist ───────────────────────────────────────────────────────────────

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Batch Details')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('batch_code')
                                ->label('Batch Code')
                                ->weight('bold')
                                ->fontFamily('mono'),

                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'pending_review' => 'Pending Review',
                                    'approved'       => 'Approved',
                                    'rejected'       => 'Rejected',
                                    default          => $state,
                                })
                                ->color(fn ($state) => match ($state) {
                                    'pending_review' => 'warning',
                                    'approved'       => 'success',
                                    'rejected'       => 'danger',
                                    default          => 'gray',
                                }),

                            TextEntry::make('source_file_name')
                                ->label('Source File')
                                ->placeholder('—'),
                        ]),

                    Grid::make(3)
                        ->schema([
                            TextEntry::make('importer.name')
                                ->label('Imported by'),

                            TextEntry::make('imported_at')
                                ->label('Imported at')
                                ->dateTime('d M Y H:i'),

                            TextEntry::make('notes_importer')
                                ->label('Importer notes')
                                ->placeholder('—'),
                        ]),

                    Grid::make(3)
                        ->schema([
                            TextEntry::make('approver.name')
                                ->label('Reviewed by')
                                ->placeholder('—'),

                            TextEntry::make('reviewed_at')
                                ->label('Reviewed at')
                                ->dateTime('d M Y H:i')
                                ->placeholder('—'),

                            TextEntry::make('notes_reviewer')
                                ->label('Reviewer notes')
                                ->placeholder('—'),
                        ]),
                ]),

            Section::make('Import Summary')
                ->schema([
                    TextEntry::make('summary_json')
                        ->label('')
                        ->formatStateUsing(function (ImportBatch $record) {
                            if (! $record->summary_json) {
                                return '—';
                            }
                            $rows = '';
                            foreach ($record->summary_json as $sheet => $counts) {
                                $ins  = $counts['inserted'] ?? 0;
                                $skip = $counts['skipped']  ?? 0;
                                $rows .= "<tr>
                                    <td style='padding:6px 12px; font-weight:600;'>{$sheet}</td>
                                    <td style='padding:6px 12px; text-align:center;'>{$ins}</td>
                                    <td style='padding:6px 12px; text-align:center; color:#6b7280;'>{$skip}</td>
                                </tr>";
                            }
                            $total = $record->totalRecords();
                            return new \Illuminate\Support\HtmlString("
                                <table style='width:100%; border-collapse:collapse; font-size:0.875rem;'>
                                    <thead>
                                        <tr style='border-bottom:2px solid #e5e7eb;'>
                                            <th style='padding:6px 12px; text-align:left;'>Sheet</th>
                                            <th style='padding:6px 12px; text-align:center;'>Inserted</th>
                                            <th style='padding:6px 12px; text-align:center;'>Skipped</th>
                                        </tr>
                                    </thead>
                                    <tbody>{$rows}</tbody>
                                    <tfoot>
                                        <tr style='border-top:2px solid #e5e7eb; font-weight:700;'>
                                            <td style='padding:6px 12px;'>Total</td>
                                            <td style='padding:6px 12px; text-align:center;'>{$total}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            ");
                        })
                        ->html(),
                ]),
        ]);
    }
}

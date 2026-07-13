<?php

namespace App\Filament\Resources\ImportBatches;

use App\Filament\Resources\ImportBatches\Pages\ListImportBatches;
use App\Filament\Resources\ImportBatches\Pages\ViewImportBatch;
use App\Models\ImportBatch;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ImportBatchResource extends Resource
{
    protected static ?string $model = ImportBatch::class;

    protected static string | \BackedEnum | null $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static string | \UnitEnum | null   $navigationGroup = 'Underwritten';
    protected static ?string                     $navigationLabel = 'Import Batches';
    protected static ?int                        $navigationSort  = 3;

    // ── Table ──────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_code')
                    ->label('Batch')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->fontFamily('mono'),

                TextColumn::make('importer.name')
                    ->label('Imported by')
                    ->sortable(),

                TextColumn::make('imported_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('source_file_name')
                    ->label('File')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->source_file_name)
                    ->placeholder('—'),

                TextColumn::make('total_records')
                    ->label('Records')
                    ->getStateUsing(fn (ImportBatch $record) => $record->totalRecords())
                    ->alignCenter(),

                TextColumn::make('status')
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

                TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('imported_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_review' => 'Pending Review',
                        'approved'       => 'Approved',
                        'rejected'       => 'Rejected',
                    ]),
            ])
            ->recordUrl(fn (ImportBatch $record) => static::getUrl('view', ['record' => $record]));
    }

    // ── Pages ──────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index' => ListImportBatches::route('/'),
            'view'  => ViewImportBatch::route('/{record}'),
        ];
    }
}

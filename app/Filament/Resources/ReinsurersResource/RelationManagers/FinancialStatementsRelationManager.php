<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use App\Models\ReinsurerFinancialStatement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Schema;
use Filament\Resources\RelationManagers\RelationManager;

class FinancialStatementsRelationManager extends RelationManager
{
    protected static string $relationship = 'financialStatements';
    protected static ?string $title  = 'Financial Statements';
    protected static ?string $recordTitleAttribute = 'start_date';
    protected static ?string $icon = 'heroicon-o-presentation-chart-line';

    /* ==========  TABLA  ========== */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo
                /*  Fechas  */
                TextColumn::make('start_date')
                    ->label('Start date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End date')
                    ->date()
                    ->sortable(),

                /*  Columna PDF + nombre de archivo  */
                TextColumn::make('document_path')
                    ->label('Document')
                    // ——— ÍCONO PDF
                    ->icon('heroicon-o-document')   // «document-text» si usas Heroicons Mini
                    ->iconColor('danger')           // rojo, igual que en Corporate Docs
                    ->color('danger')
                    // ——— SOLO EL NOMBRE DEL ARCHIVO
                    ->formatStateUsing(fn ($state) => \Illuminate\Support\Str::afterLast($state, '/'))
                    // ——— LINK
                   ->url(function ($record) {
                        // ───────── URL absoluta vs. path en S3 ─────────
                        if (\Illuminate\Support\Str::startsWith(
                            $record->document_path,
                            ['http://', 'https://']
                        )) {
                            return $record->document_path;       // ya es URL completa
                        }

                        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */  // ✅ anotación
                        $s3 = \Illuminate\Support\Facades\Storage::disk('s3');

                        return $s3->url($record->document_path); // genera la URL firmada
                    })
                    ->openUrlInNewTab()
                    ->tooltip('View PDF'),
            ])
            ->defaultSort('start_date', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Financial Statement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /* ==========  FORMULARIO  ========== */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('Start date')
                    ->required(),

                DatePicker::make('end_date')
                    ->label('End date')
                    ->required(),

                FileUpload::make('document_path')
                    ->label('File (PDF)')
                    ->disk('s3')
                    ->directory('reinsurers/financials_statements')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(fn ($record) => $record === null || blank($record->document_path))
                    ->getUploadedFileNameForStorageUsing(function ($file, $record, $set, $get) {
                        $reinsurer = $this->getOwnerRecord();
                        $reinsurerName = $reinsurer?->short_name ?? 'unknown';

                        $startDate = $get('start_date')
                            ? \Carbon\Carbon::parse($get('start_date'))->format('Ymd')
                            : 'nodate';

                        $endDate = $get('end_date')
                            ? \Carbon\Carbon::parse($get('end_date'))->format('Ymd')
                            : 'nodate';

                        $extension = $file->getClientOriginalExtension();

                        return "{$reinsurerName}--{$startDate} to {$endDate}.{$extension}";
                    })
                    ->downloadable()
                    ->openable()
                    ->previewable(true)
                    ->dehydrateStateUsing(function ($state, ?\App\Models\ReinsurerFinancialStatement $record) {
                        // 1) Si no se sube nada nuevo y ya hay archivo guardado → conserva el existente
                        if (blank($state) && $record?->document_path) {
                            return $record->document_path;
                        }

                        // 2) Si viene como array ["uuid" => "ruta/del/archivo.pdf"]
                        if (is_array($state)) {
                            // Nos quedamos con el primer valor del array
                            $state = array_values($state)[0] ?? null;
                        }

                        // 3) Ahora $state ya es string (o null)
                        return $state;
                    })
                    ->hint(fn ($record) => $record?->document_path
                        ? 'Existing file: ' . basename($record->document_path)
                        : 'No file uploaded yet.'
                    )
                    ->helperText('Only PDF files are allowed.')
                    ->columnSpanFull(),



                    







            ]);
    }
}

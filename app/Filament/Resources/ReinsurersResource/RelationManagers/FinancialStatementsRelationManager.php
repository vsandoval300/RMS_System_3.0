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
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;


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
                    // Ícono dinámico
                    ->icon(fn ($state, $record) =>
                        $record->document_path ? 'heroicon-o-document-text' : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state, $record) =>
                        $record->document_path ? 'primary' : 'danger'
                    )
                    ->tooltip(fn ($state, $record) =>
                        $record->document_path ? 'View PDF' : 'No document available'
                    )
                    // Solo el nombre del archivo
                    ->formatStateUsing(fn ($state) =>
                        $state ? Str::afterLast($state, '/') : '—'
                    )
                    ->extraAttributes(['class' => 'cursor-pointer'])
                    ->searchable()
                    ->sortable()
                    ->action(
                        Action::make('viewPdf')
                            ->label('View PDF')
                            ->hidden(fn ($record) => blank($record->document_path))
                            ->modalHeading(fn ($record) => "PDF – {$record->id}")
                            ->modalWidth('7xl')
                            ->modalSubmitAction(false)
                            ->modalContent(function ($record) {
                                $path = $record->document_path;

                                if (blank($path)) {
                                    return new HtmlString('<p>No document available.</p>');
                                }

                                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                                $disk = Storage::disk('s3');

                                // Si viene una URL completa, intentamos recuperar solo la "key" del objeto
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    // Ejemplo simple: quitar dominio de S3 y quedarnos con la key
                                    $parsed = parse_url($path);
                                    $key = ltrim($parsed['path'] ?? '', '/');
                                } else {
                                    $key = $path;
                                }

                                if (! $disk->exists($key)) {
                                    return new HtmlString(
                                        '<p>The PDF file does not exist in S3.</p>'
                                        .'<p><code>' . e($key) . '</code></p>'
                                    );
                                }

                                // 🔥 Siempre generamos una URL temporal con headers "inline"
                                $url = $disk->temporaryUrl(
                                    $key,
                                    now()->addMinutes(10),
                                    [
                                        'ResponseContentType'        => 'application/pdf',
                                        'ResponseContentDisposition' => 'inline; filename="'.basename($key).'"',
                                    ]
                                );

                                return view('filament.components.pdf-viewer', [
                                    'url' => $url,
                                ]);
                            })
                    )





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

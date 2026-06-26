<?php

namespace App\Filament\Resources\Reinsurers\RelationManagers;

use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;


class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Corporate Documents';
    /** ← NUEVO: etiqueta del botón */
    protected static ?string $createButtonLabel = 'New corporate doc';
    protected static string | \BackedEnum | null $icon = 'heroicon-o-document-text';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('documentCorpType'); // 👈 evita N+1
    }

    /* ---------- Tabla ---------- */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo

                // 🔧 1) Usamos TextColumn y le añadimos ->date()
                TextColumn::make('stamp_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                // 🔧 2) Nombre de la relación correcto
                TextColumn::make('documentCorpType.name')
                    ->label('Type')
                    ->searchable(),

                // 👉 Nombre del archivo (solo texto)
                TextColumn::make('document_path')
                    ->label('File')
                    // Muestra solo el nombre del archivo
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—')
                    ->icon(fn ($state, $record) =>
                        $record->document_path ? 'heroicon-o-document-text' : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state, $record) =>
                        $record->document_path ? 'primary' : 'danger'
                    )
                    ->tooltip(fn ($state, $record) =>
                        $record->document_path ? 'View PDF' : 'No document available'
                    )
                    ->extraAttributes([
                        'class' => 'cursor-pointer', // que parezca clickeable
                    ])
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

                                /** @var FilesystemAdapter $disk */
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
                    ),
            ])
            ->defaultSort('stamp_date', 'asc')
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Add document')
                    ->label('New Corporate Document'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);






                // Nos quedamos aqui en que no se renderiza la columna
              /*  TextColumn::make('document_path')
                    ->label('File')
                    ->icon('heroicon-o-document') // ícono PDF
                    ->color('danger')
                    ->url(function ($record) {
                        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 
                        $s3 = Storage::disk('s3');

                        return Str::startsWith(
                            $record->document_path,
                            ['http://', 'https://']
                        )
                            ? $record->document_path
                            : $s3->url($record->document_path);
                    })
                    ->openUrlInNewTab()
                    ->tooltip('View PDF')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—') // 👈 ahora sí funciona
                    ->searchable()
                    ->sortable(),

                ])
                ->defaultSort('stamp_date', 'asc')
                ->headerActions([
                    Tables\Actions\CreateAction::make()
                        ->modalHeading('Add document')
                        ->label('New Corporate Document'), // ← Cambias el texto aquí
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make(),
                ]); */
    }

    /* ---------- Formulario ---------- */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    DatePicker::make('stamp_date')
                        ->label('Date')
                        ->required(),

                    Select::make('document_type_id')
                        ->label('Type')
                        ->options(fn () =>
                            DocumentType::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

                FileUpload::make('document_path')
                    ->label('File (PDF)')
                    ->disk('s3')
                    ->directory('reinsurers/corporate_documents')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf'])
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get) {
                        $documentType = DocumentType::find($get('document_type_id'));
                        $acronym = $documentType?->acronym ?? 'DOC';
                        $date = now()->format('Ymd');
                        $random = rand(10000, 99999);
                        return "{$acronym}-{$date}-{$random}.pdf";
                    })
                    ->downloadable()
                    ->openable()
                    ->previewable(true)
                    ->hint(fn ($record) => $record?->document_path
                        ? 'Existing file: ' . basename($record->document_path)
                        : 'No file uploaded yet.'
                    )
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Only PDF files are allowed.')
                    ->columnSpanFull(),
            ]);
    }
}



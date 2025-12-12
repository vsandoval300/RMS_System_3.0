<?php

namespace App\Filament\Resources\TreatyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str; // 👈 agrega esto
use Illuminate\Support\HtmlString;

class DocsRelationManager extends RelationManager
{
    protected static string $relationship = 'docs'; // 👈 coincide con Treaty::docs()

    protected static ?string $title = 'Documents';

    protected static ?string $recordTitleAttribute = 'description';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(12)->schema([
                Forms\Components\TextInput::make('index')
                    ->label('Index')
                    ->numeric()
                    ->readOnly()
                    ->default(function (self $livewire) {
                        // siguiente índice dentro del mismo Treaty
                        $max = $livewire->ownerRecord
                            ? $livewire->ownerRecord->docs()->max('index')
                            : 0;

                        return ($max ?? 0) + 1;
                    })
                    ->columnSpan(2),

                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->maxLength(255)
                    ->columnSpan(10),

                Forms\Components\FileUpload::make('document_path')
                    ->label('Document (PDF)')
                    ->disk('s3')                 // 👈 cambia si usas otro disk
                    ->directory('reinsurers/Treaties')   // carpeta en S3
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/pdf'])
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        // Obtenemos el treaty_code del registro padre (Treaty)
                        $treaty = $this->getOwnerRecord();    // RelationManager => Treaty
                        $treatyCode = $treaty?->treaty_code ?? 'TREATY';

                        // Normalizamos por si acaso
                        $treatyCode = str_replace(['/', '\\', ' '], '-', $treatyCode);

                        // Extensión del archivo
                        $extension = $file->getClientOriginalExtension() ?: 'pdf';

                        // Clave aleatoria de 6 caracteres (A-Z0-9)
                        $suffix = strtoupper(Str::random(6));

                        // Nombre final: TTY-2025-ADA005-001-ABC123.pdf
                        return "{$treatyCode}-{$suffix}.{$extension}";
                    })
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('Index')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    //->limit(40)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('document_path')
                    ->label('File')
                    ->formatStateUsing(fn ($state) =>
                        $state ? basename($state) : '—'
                    )
                    ->icon(fn ($state) =>
                        $state ? 'heroicon-o-document-text' : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state) =>
                        $state ? 'primary' : 'danger'
                    )
                    ->extraAttributes(['class' => 'cursor-pointer'])
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
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add document')
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

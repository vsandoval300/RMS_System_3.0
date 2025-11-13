<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use App\Models\DocumentType;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;   // ✅ único import de columnas
use Filament\Forms\Components\Grid;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Str;


class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Corporate Documents';
    /** ← NUEVO: etiqueta del botón */
    protected static ?string $createButtonLabel = 'New corporate doc';
    protected static ?string $icon = 'heroicon-o-document-text';


    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with('documentCorpType'); // 👈 evita N+1
    }

    /* ---------- Tabla ---------- */
    public function table(Tables\Table $table): Tables\Table
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

                // Nos quedamos aqui en que no se renderiza la columna
               TextColumn::make('document_path')
                    ->label('File')
                    ->icon('heroicon-o-document') // ícono PDF
                    ->color('danger')
                    ->url(function ($record) {
                        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
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
                ]);
    }

    /* ---------- Formulario ---------- */
    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
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
                    ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get) {
                        $documentType = \App\Models\DocumentType::find($get('document_type_id'));
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



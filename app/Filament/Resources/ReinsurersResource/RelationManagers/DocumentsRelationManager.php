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

    /* ---------- Tabla ---------- */
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // 🔧 1) Usamos TextColumn y le añadimos ->date()
                TextColumn::make('stamp_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                // 🔧 2) Nombre de la relación correcto
                TextColumn::make('documentCorpType.name')
                    ->label('Type')
                    ->searchable(),

                IconColumn::make('document_path')
                ->label('')                         // sin encabezado
                ->icon('heroicon-o-document')       // ícono PDF
                ->color('danger')
                ->url(function ($record) {
                    /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */   // ← anotación
                    $s3 = Storage::disk('s3');                                // ← ahora el IDE sabe su tipo

                    return Str::startsWith(
                        $record->document_path,
                        ['http://', 'https://']
                    )
                        ? $record->document_path            // ya es URL completa
                        : $s3->url($record->document_path); // genera URL firmada
                })
                ->openUrlInNewTab()
                ->tooltip('View PDF'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Add document')
                    ->label('New corporate doc'), // ← Cambias el texto aquí
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                    ->label('File')
                    ->disk('s3')
                    ->directory('reinsurers/docs')
                    ->visibility('private')
                    ->required(),
            ]);
    }
}



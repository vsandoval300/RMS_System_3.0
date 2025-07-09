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
    protected static ?string $title        = 'Financial Statements';

    /* ==========  TABLA  ========== */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Actions\CreateAction::make()->label('New statement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                    ->disk('s3')                         // o 'public' según tu storage
                    ->directory('reinsurers/financials') // carpeta en el bucket
                    ->visibility('private')              // cámbialo si tu bucket es público
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),
            ]);
    }
}

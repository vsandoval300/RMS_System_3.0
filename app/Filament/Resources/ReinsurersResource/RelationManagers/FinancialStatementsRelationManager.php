<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use App\Models\ReinsurerFinancialStatement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Schema;
use Filament\Resources\RelationManagers\RelationManager;

class FinancialStatementsRelationManager extends RelationManager
{
    protected static string $relationship = 'financialStatements';
    protected static ?string $title = 'Financial Statements';

    /* ---------- Tabla ---------- */
    public function table(Table $table): Table
    {
        // 1. Averiguamos los campos de la tabla
        $cols = Schema::getColumnListing('reinsurer_financials');

        // 2. Mapeamos cada campo a un TextColumn simple
        $columns = collect($cols)->map(function ($col) {
            // Puedes filtrar columnas que NO quieras mostrar:
            if (in_array($col, ['id', 'reinsurer_id', 'created_at', 'updated_at', 'deleted_at'])) {
                return null;
            }

            // Ejemplo: formatear fechas
            if (str_ends_with($col, '_date')) {
                return TextColumn::make($col)->date();
            }

            return TextColumn::make($col)->wrap();
        })->filter()->values()->all(); // quitamos nulls

        return $table
            ->columns($columns)
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New statement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    /* ---------- Formulario ---------- */
    public function form(Form $form): Form
    {
        // Campos que no queremos en el formulario
        $skip = ['id', 'reinsurer_id', 'created_at', 'updated_at', 'deleted_at'];

        $elements = collect(Schema::getColumnListing('reinsurer_financials'))
            ->reject(fn ($col) => in_array($col, $skip))
            ->map(function ($col) {
                // Detecta fechas
                if (str_ends_with($col, '_date')) {
                    return DatePicker::make($col)->required();
                }

                // Detecta el PDF
                if ($col === 'document_path') {
                    return FileUpload::make($col)
                        ->disk('public')                // o s3
                        ->directory('reinsurers/fin')
                        ->visibility('private')
                        ->required();
                }

                // Genérico
                return TextInput::make($col);
            })->values()->all();

        return $form->schema($elements);
    }
}
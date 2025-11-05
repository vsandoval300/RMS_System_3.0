<?php

namespace App\Filament\Resources\ClientsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class IndustriesRelationManager extends RelationManager
{
    protected static string $relationship = 'industries';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('short_name')
                        ->label('Short Name')
                        ->required()
                        ->unique()
                        ->live(onBlur: false)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.'),
                       
                    
                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->columnSpan('full')
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Industry')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('description')
                ->wrap()                                 // ✔ permite salto de línea
                ->extraAttributes([
                    'style' => 'max-width: 950px; white-space: normal;', // ancho deseado
                ]),
        ])

        /* ───── Acciones del encabezado (arriba a la derecha) ───── */
        ->headerActions([
            Tables\Actions\AttachAction::make()
                ->label('Add industry')      // ← texto del botón
                ->modalHeading('Attach Industry')
                ->preloadRecordSelect()      // muestra 1ª página al abrir
                ->recordSelectSearchColumns(['name', 'description']), // opcional
                // ->multiple()               // marca esto si quieres adjuntar varios de golpe
        ])

        /* ───── Acciones por fila ───── */
        ->actions([
            Tables\Actions\DetachAction::make()
                ->label('Delete') // quita el texto "Detach"
                ->icon('heroicon-o-trash') // icono de bote de basura
                ->color('danger') // mismo rojo (opcional, ya suele ser danger)
                ->link(), // que sea sólo icono, no botón con borde
            // Tables\Actions\EditAction::make(),
        ])

        /* ───── Acciones masivas (checkbox) ───── */
        ->bulkActions([
            Tables\Actions\DetachBulkAction::make()
                ->label('Delete') // o el texto que quieras
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ]);

            



    }
}

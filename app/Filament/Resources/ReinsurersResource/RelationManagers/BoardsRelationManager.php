<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

class BoardsRelationManager extends RelationManager
{
    protected static string $relationship = 'boards';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('index')
                ->required()
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // 👇 SIN type-hint en el parámetro
            ->modifyQueryUsing(fn ($query) => $query->with('directors'))
            ->recordTitleAttribute('index')
            ->columns([
                /* ───── Index ───── */
                TextColumn::make('index')
                    ->label('Index')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable(),

                /* ───── Appointment ───── */
                TextColumn::make('pivot.appt_date')
                    ->label('Appointment')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->date(),

                /* ───── Directors ───── */
                TextColumn::make('directors')
                    ->label('Directors')
                    ->verticalAlignment(VerticalAlignment::Start)  
                    ->html()
                    ->state(function ($record) {
                        return $record->directors        // colección de modelos Director
                            ->map(fn ($d) => trim($d->name . ' ' . $d->surname))  // «Nombre Apellido»
                            ->implode('<br>');           // separa cada uno con salto de línea
                    }),

                /* ───── Occupations ───── */
                TextColumn::make('occupations')
                    ->label('Occupation')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->html()
                    ->state(fn ($record) =>
                        $record->directors
                            ->map(fn ($d) => e($d->occupation ?? '—'))
                            ->implode('<br>')
                    ),

            ])
            ->defaultSort('index', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New board'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use App\Models\Holding;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;

class ReinsurerHoldingsRelationManager extends RelationManager
{
    protected static string $relationship = 'reinsurerHoldings';
    protected static ?string $title = 'Holdings';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('holding_id')
                ->label('Holding')
                ->relationship('holding', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('percentage')
                ->label('Participation Percentage')
                ->suffix('%')
                ->required()
                ->live()
                ->minValue(0)
                ->maxValue(100)
                ->step(0.01)
                ->mask(RawJs::make('$money($input, ".", ",", 2)')) // se ve como 70.00
                ->reactive()
                ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100, 2) : null) // decimal → porcentaje
                ->dehydrateStateUsing(fn ($state) => floatval(str_replace(',', '', $state)) / 100) // porcentaje → decimal
                ->columnSpan(1),

        ]);
    }




    public function table(Table $table): Table
    {
        return $table
            // 🏎 Eager-load para evitar N+1
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with(['holding.country', 'holding.client'])
            )

            // Muestra el nombre del holding como título de registro
            ->recordTitleAttribute('holding.name')

            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo
                // Holding
                TextColumn::make('holding.name')
                    ->label('Holding')
                    ->searchable()
                    ->sortable(),

                // País
                TextColumn::make('holding.country.name')
                    ->label('Country')
                    ->sortable(),

                // Cliente
                TextColumn::make('holding.client.name')
                    ->label('Client')
                    ->sortable(),

                // % formateado
                TextColumn::make('percentage')
                    ->label('Participation Share')
                    ->alignCenter()                      // alinea a la derecha
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%')
                    ->sortable(),
            ])

            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Holding'),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

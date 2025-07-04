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

class ReinsurerHoldingsRelationManager extends RelationManager
{
    protected static string $relationship = 'reinsurerHoldings';
    protected static ?string $title = 'Holdings';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('holding_id')
                ->label('Holding')
                ->options(Holding::pluck('name', 'id'))
                ->required(),
            TextInput::make('percentage')
                ->numeric()->minValue(0)->maxValue(100)->required(),
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
                    ->label('%')
                    ->alignEnd()                       // alinea a la derecha
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%')
                    ->sortable(),
            ])

            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
